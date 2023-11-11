<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\SettingsUserController;
use App\Models\UserMeta;

class SettingsDatabaseController extends Controller
{
    // Define the custom database connection name
    protected $connection = 'smAppTemplate';

    // Fill created_at
    public $timestamps = true;

    /**
     * Display the database settings page.
     *
     * Retrieve departments and companies from the database and pass them to the view.
     *
     * @return \Illuminate\View\View
     */
    public function index() {
        $departments = DB::connection($this->connection)
            ->table('wlsm_departments')
            ->orderBy('department_id', 'asc')
            ->get();

        $companies = DB::connection($this->connection)
            ->table('wlsm_companies')
            ->orderBy('company_id', 'asc')
            ->get();

        return view('settings.database', compact('departments', 'companies'));
    }

    /**
     * Update the aliases and status of the departments.
     *
     * Validate the incoming request data, update the 'department_alias' and 'status' for each department
     * in the 'wlsm_departments' table, and redirect the user back to the previous page with a success message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateDepartments(Request $request)
    {
        // Update department aliases
        foreach ($request->aliases as $id => $alias) {
            DB::connection($this->connection)
                ->table('wlsm_departments')
                ->where('id', $id)
                ->update(['department_alias' => $alias]);
        }

        // Update department status
        foreach ($request->status as $id => $status) {
            DB::connection($this->connection)
                ->table('wlsm_departments')
                ->where('id', $id)
                ->update(['status' => $status ? 1 : 0]);
        }

        return redirect()->back()->with('active_tab', 'departments')->with('success', 'Departamentos atualizados');
    }

    /**
     * Update the aliases and status of the companies.
     *
     * Validate the incoming request data, update the 'company_alias' and 'status' for each company
     * in the 'wlsm_companies' table, and redirect the user back to the previous page with a success message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateCompanies(Request $request)
    {

        // Update company aliases
        foreach ($request->aliases as $id => $alias) {
            DB::connection($this->connection)
                ->table('wlsm_companies')
                ->where('id', $id)
                ->update(['company_alias' => $alias]);
        }

        // Update company status
        foreach ($request->status as $id => $status) {
            DB::connection($this->connection)
                ->table('wlsm_companies')
                ->where('id', $id)
                ->update(['status' => $status ? 1 : 0]);
        }

        // Get IDs of active companies
        $getActiveCompanies = getActiveCompanies();
        if (is_object($getActiveCompanies)) {
            $extractCompanyIds = $getActiveCompanies->pluck('company_id')->map(function ($value) {
                return (int) $value;
            })->all();
        }

        // Create an instance of SettingsUserController and call updateUserMeta
        $userController = new SettingsUserController();
        $userController->updateUserMeta(1, 'companies', json_encode($extractCompanyIds));

        return redirect()->back()->with('active_tab', 'companies')->with('success', 'Empresas atualizadas');
    }

    /**
     * Process API data for a given period.
     *
     * @param string $meantime The period to process, in 'Y-m' format.
     * @return bool True on success, false on failure.
     */
    public function updateSales($meantime, $database = null)
    {
        $start_time = microtime(true);

        $now = now();

        set_time_limit(600);

        // Set the database connection name dynamically
        if ($database) {
            $databaseName = 'smApp' . $database;
            config(['database.connections.smAppTemplate.database' => $databaseName]);
        }

        //$responseDataToMe = array();

        // Validate and set the meantime to the current month-year if not provided or invalid
        $meantime = !empty($meantime) && strlen($meantime) === 7 ? $meantime : now()->format('Y-m');

        // Extract the year and month from the meantime
        [$year, $month] = explode('-', $meantime);

        // Determine the first and last day of the month
        $firstDay = $year . '-' . $month . '-01';
        $lastDay = $year . '-' . $month . '-' . date('t', strtotime($firstDay));

        // Initialize variables for data processing
        $hasMorePages = true;
        $slept = $countInserts = 0;
        $pageRead = [];
        $companyData = $departmentData = [];

        $ERPdata = getERPdata($database);

        // API endpoint details
        $endpoint = 'vendas_produtos';
        $customer = $ERPdata['customer'];
        $username = $ERPdata['username'];
        $password = $ERPdata['password'];
        $term = $ERPdata['term'];

        $url = 'https://' . $customer . '.sysmo.com.br:8443/sysmo-integrador-api/api/integradorService/supera_metas.' . $endpoint;
        $pageNumber = 1;
        $pageSize = 1000;

        // Initialize cURL session
        $curl = curl_init($url);

        // Set cURL options
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POST => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERPWD => $username . ':' . $password
        ]);

        // Check for cURL initialization errors
        if (curl_errno($curl) || !$curl) {
            \Log::error('cURL initialization error: ' . curl_error($curl));
            curl_close($curl);
            return false;
        }



        // Loop to fetch data from the API
        do {
            // Define the JSON payload for the API request
            $data = [
                "pagina" => strval($pageNumber),
                "tamanho_pagina" => strval($pageSize),
                "data_inicial" => strval($firstDay),
                "data_final" => strval($lastDay),
                "tipo_consulta" => strval($term)
            ];

            // Convert the data to JSON and set it as the cURL POSTFIELDS
            $jsonData = json_encode($data);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);

            // Execute the cURL request
            $currentResponse = curl_exec($curl);

            // Decode the response
            $responseData = json_decode($currentResponse, true);

            if ( isset($responseData['codigo']) ) {
                $hasMorePages = false;

                curl_close($curl);

                $end_time = microtime(true);
                $elapsed_time = ($end_time - $start_time);
                $elapsed_time = $elapsed_time >= 60 ? numberFormat( ($elapsed_time / 60), 2)." minutes" : numberFormat($elapsed_time, 0)." seconds";

                DB::disconnect($this->connection);

                return response()->json([
                    'success' => false,
                    'message' => 'Dados indisponíveis. Tente novamente mais tarde.',
                    'elapsed_time' => $elapsed_time,
                    'slept' => $slept.' seconds',
                    'response' => json_encode($responseData)
                ]);

                exit;
            }

            $responseDataArr = $responseData[0]['dados'] ?? '';

            // Process the response if it contains the expected data
            if (!empty($responseDataArr) && is_array($responseDataArr) && !in_array($pageNumber, $pageRead)) {
                $totalPages = isset($responseData[0]['total_paginas']) ? intval($responseData[0]['total_paginas']) : 1;
                $currentPage = isset($responseData[0]['pagina']) ? intval($responseData[0]['pagina']) : 1;

                if($currentPage === 1 ){
                    SettingsDatabaseController::deleteOldData($meantime);
                }

                // Store the current page number to avoid processing it again
                $pageRead[] = $currentPage;

                //$responseDataToMe[] = $responseDataArr;

                $salesData = [];

                // Process each record in the responseData
                foreach ($responseDataArr as $values) {
                    // Extract department and company information
                    if ($term == 'departamento') {
                        $departmentID = isset($values['departamento']) ? intval($values['departamento']) : 0;
                        $departmentDescription = isset($values['departamento_descricao']) ? $values['departamento_descricao'] : '';
                    } else {
                        $categoryID = isset($values['categoria']) ? intval($values['categoria']) : 0;
                        $departmentID = isset($values['departamento']) ? intval($values['departamento'] . $categoryID) : 0;

                        $departmentDescription = isset($values['departamento_descricao']) ? $values['departamento_descricao'] : '';

                        $departmentDescription = isset($values['categoria_descricao']) ? $departmentDescription . '<br> - ' . $values['categoria_descricao'] : '';
                    }

                    $companyID = isset($values['empresa']) ? intval($values['empresa']) : 0;

                    $dateSale = isset($values['data_venda']) ? $values['data_venda'] : '';
                    $netValue = isset($values['valor_liquido']) && is_numeric($values['valor_liquido']) ? floatval($values['valor_liquido']) : 0;

                    $companyData[$companyID] = [
                        'company_id' => $companyID,
                        'company_name' => $companyID,
                    ];

                    $departmentData[$departmentID] = [
                        'department_id' => $departmentID,
                        'department_description' => $departmentDescription,
                        'company_ids ' => $departmentCompanyIds,
                    ];

                    $salesData[] = [
                        'company_id' => $companyID,
                        'department_id' => $departmentID,
                        'net_value' => $netValue,
                        'date_sale' => $dateSale,
                        'created_at' => $now
                    ];
                }
                // Check if there are more pages to fetch
                if ($pageNumber >= $totalPages) {
                    $hasMorePages = false;
                } else {
                    $pageNumber++;
                }

                // Insert the extracted data into the wlsm_sales table
                try {
                    DB::connection($this->connection)->table('wlsm_sales')->insert($salesData);
                } catch (\Exception $e) {
                    \Log::error('Failed to insert into wlsm_sales: ' . $e->getMessage());
                }
            } else {
                // If the response doesn't contain the expected data, wait for 15 seconds before retrying
                sleep(15);
                $slept += 15;
            }

            // If the function has been sleeping for more than 150 seconds in total, exit the loop
            if ($slept >= 150) {
                //break;

                // Close the cURL session
                curl_close($curl);

                $end_time = microtime(true);
                $elapsed_time = ($end_time - $start_time);
                $elapsed_time = $elapsed_time >= 60 ? numberFormat( ($elapsed_time / 60), 2)." minutes" : numberFormat($elapsed_time, 0)." seconds";

                return response()->json([
                    'success' => false,
                    'message' => 'Tempo excedido e processo interrompido. Tente novamente mais tarde.',
                    'elapsed_time' => $elapsed_time,
                    'slept' => $slept.' seconds'
                    //'response' => $responseDataToMe
                ]);

                exit;
            }

        } while ($hasMorePages);

        // Close the cURL session
        curl_close($curl);

        // Fetch existing companies
        $existingCompanies = DB::connection($this->connection)->table('wlsm_companies')
            ->whereIn('company_id', array_keys($companyData))
            ->pluck('company_name', 'company_id');

        // Update or insert companies
        foreach ($companyData as $companyID => $data) {
            if (isset($existingCompanies[$companyID])) {
                // Update existing company
                DB::connection($this->connection)->table('wlsm_companies')
                    ->where('company_id', $companyID)
                    ->update($data);
            } else {
                // Insert new company
                DB::connection($this->connection)->table('wlsm_companies')
                    ->insert($data);
            }
        }

        // Fetch existing departments
        $existingDepartments = DB::connection($this->connection)->table('wlsm_departments')
            ->whereIn('department_id', array_keys($departmentData))
            ->pluck('department_description', 'department_id');

        // Update or insert departments
        foreach ($departmentData as $departmentID => $data) {
            if (isset($existingDepartments[$departmentID])) {
                // Update existing department
                DB::connection($this->connection)->table('wlsm_departments')
                    ->where('department_id', $departmentID)
                    ->update($data);
            } else {
                // Insert new department
                DB::connection($this->connection)->table('wlsm_departments')
                    ->insert($data);
            }
        }

        $end_time = microtime(true);
        $elapsed_time = ($end_time - $start_time);
        $elapsed_time = $elapsed_time >= 60 ? numberFormat( ($elapsed_time / 60), 2)." minutes" : numberFormat($elapsed_time, 0)." seconds";

        DB::disconnect($this->connection);

        return response()->json([
            'success' => true,
            'message' => 'Dados processados com sucesso!',
            'elapsed_time' => $elapsed_time,
            'slept' => $slept.' seconds',
            //'response' => json_encode($responseDataToMe)
        ]);
    }

    public function deleteOldData($meantime){
        // Delete old data for the given period from wlsm_sales
        try {
            DB::connection($this->connection)->table('wlsm_sales')->where('date_sale', 'like', $meantime . '%')->delete();
        } catch (\Exception $e) {
            \Log::error('Failed to delete from wlsm_sales: ' . $e->getMessage());
            return false;
        }
    }

}
