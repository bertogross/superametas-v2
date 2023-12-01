<?php

namespace App\Http\Controllers;

use App\Models\UserMeta;
use Illuminate\Http\Request;
use App\Models\SettingsDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\SettingsUserController;

class SettingsDatabaseController extends Controller
{


    /**
     * Display the database settings page.
     * Retrieve departments and companies from the database and pass them to the view.
     */
    public function index() {
        $departments = DB::connection('smAppTemplate')
            ->table('wlsm_departments')
            ->orderBy('department_id', 'asc')
            ->get();

        $companies = DB::connection('smAppTemplate')
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
     */
    public function updateDepartments(Request $request)
    {
        // Update department aliases
        foreach ($request->aliases as $id => $alias) {
            DB::connection('smAppTemplate')
                ->table('wlsm_departments')
                ->where('id', $id)
                ->update(['department_alias' => $alias]);
        }

        // Update department status
        foreach ($request->status as $id => $status) {
            DB::connection('smAppTemplate')
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
     */
    public function updateCompanies(Request $request)
    {

        // Update company aliases
        foreach ($request->aliases as $id => $alias) {
            DB::connection('smAppTemplate')
                ->table('wlsm_companies')
                ->where('id', $id)
                ->update(['company_alias' => $alias]);
        }

        // Update company status
        foreach ($request->status as $id => $status) {
            DB::connection('smAppTemplate')
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
     * https://www.sysmo.com.br/ajuda/integracoes/integracao_g/supera_metas/layout.html#vendas_departamento
     */
    public function updateSalesFromSysmo($meantime = null, $database = null)
    {
        /*
        // Check if today is the first day of the month
        $today = now();
        if ($today->day == 1) {
            // Set meantime for the previous month
            $previousMonth = $today->subMonth()->format('Y-m');

            // Run the function for the previous month
            if( SettingsDatabase::processSalesData($previousMonth, $database) ){
                // Set meantime for the current month
                $currentMonth = now()->format('Y-m');

                // Run the function for the current month
                SettingsDatabase::processSalesData($currentMonth, $database);
            }

        } else {
            // If it's not the first day, run for the current month only
            $meantime = $meantime ?? date('Y-m');
            SettingsDatabase::processSalesData($meantime, $database);
        }
        */
        $startTime = microtime(true);

        set_time_limit(600);

        // Set the database connection name dynamically
        if ($database) {
            $databaseName = 'smApp' . $database;
            config(['database.connections.smAppTemplate.database' => $databaseName]);
        }

        //$responseDataToMe = array();

        // Validate and set the meantime to the current month-year if not provided or invalid
        $meantime = $meantime && strlen($meantime) === 7 ? $meantime : date('Y-m');

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

        // Fetch existing departments with their company_ids
        $existingDepartmentsWithCompanies = DB::connection('smAppTemplate')->table('wlsm_departments')
        ->pluck('company_ids', 'department_id');

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

            // Log the raw response for debugging
            //\Log::info('API Response: ' . $currentResponse);

            // Check if the response is not empty and is a valid JSON string
            if (!$currentResponse || is_null(json_decode($currentResponse))) {
                //\Log::error('Invalid or empty JSON response.');

                curl_close($curl);

                return response()->json([
                    'success' => false,
                    'message' => 'Resposta JSON inválida ou vazia. Verifique a conexão com seu ERP ou tente novamente mais tarde.'
                ]);
            }

            // Decode the response
            $responseData = json_decode($currentResponse, true);

            // API response and errors
            if ( isset($responseData['codigo']) && $responseData['codigo'] != 'PROCESSO_EXECUTANDO' ) {
                $hasMorePages = false;

                curl_close($curl);

                $endTime = microtime(true);
                $elapsedTime = ($endTime - $startTime);
                $elapsedTime = $elapsedTime >= 60 ? numberFormat( ($elapsedTime / 60), 2)." minutes" : numberFormat($elapsedTime, 0)." seconds";

                return response()->json([
                    'success' => false,
                    'message' => 'Conexão ERP indisponível. Tente novamente mais tarde.',
                    'elapsed_time' => $elapsedTime,
                    'slept' => $slept.' seconds',
                    'response' => json_encode($responseData)
                ]);

                exit;
            }

            $responseDataArr = $responseData[0]['dados'] ?? '';

            if( empty($responseDataArr) ){

                curl_close($curl);

                return response()->json([
                    'success' => false,
                    'motive' => 'noData',
                    'message' => 'Período '.date("m/Y", strtotime($meantime)).' não possui dados'
                ]);

                exit;
            }

            // Process the response if it contains the expected data
            if (!empty($responseDataArr) && is_array($responseDataArr) && !in_array($pageNumber, $pageRead)) {
                $totalPages = isset($responseData[0]['total_paginas']) ? intval($responseData[0]['total_paginas']) : 1;
                $currentPage = isset($responseData[0]['pagina']) ? intval($responseData[0]['pagina']) : 1;

                if($currentPage === 1 ){
                    SettingsDatabase::deleteOldData($meantime);
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
                    ];

                    $salesData[] = [
                        'company_id' => $companyID,
                        'department_id' => $departmentID,
                        'net_value' => $netValue,
                        'date_sale' => $dateSale,
                        'created_at' => now()
                    ];

                    // Merge and unify company_ids for departments
                    if (isset($existingDepartmentsWithCompanies[$departmentID])) {
                        $existingCompanyIds = explode(',', $existingDepartmentsWithCompanies[$departmentID]);
                        if (!in_array($companyID, $existingCompanyIds)) {
                            $existingCompanyIds[] = $companyID;
                        }
                        $existingCompanyIds = !empty($existingCompanyIds) && is_array($existingCompanyIds) ? array_filter($existingCompanyIds) : $existingCompanyIds;
                        $existingDepartmentsWithCompanies[$departmentID] = implode(',', $existingCompanyIds);
                    } else {
                        $existingDepartmentsWithCompanies[$departmentID] = (string)$companyID;
                    }

                }
                // Check if there are more pages to fetch
                if ($pageNumber >= $totalPages) {
                    $hasMorePages = false;
                } else {
                    $pageNumber++;
                }

                // Insert the extracted data into the wlsm_sales table
                try {
                    DB::connection('smAppTemplate')->table('wlsm_sales')->insert($salesData);
                } catch (\Exception $e) {
                    \Log::error('Failed to insert into wlsm_sales: ' . $e->getMessage());
                }
            } else {
                // If the response doesn't contain the expected data, wait for 15 seconds before retrying
                sleep(15);
                $slept += 15;
            }

            // If the function has been sleeping for more than 150 seconds in total, exit the loop
            if ($slept >= 50) {

                curl_close($curl);

                DB::disconnect('smAppTemplate');

                $endTime = microtime(true);
                $elapsedTime = ($endTime - $startTime);
                $elapsedTime = $elapsedTime >= 60 ? numberFormat( ($elapsedTime / 60), 2)." minutes" : numberFormat($elapsedTime, 0)." seconds";

                return response()->json([
                    'success' => false,
                    'message' => 'Tempo excedido e processo interrompido. Tente novamente mais tarde.',
                    'elapsed_time' => $elapsedTime,
                    'slept' => $slept.' seconds'
                    //'response' => $responseDataToMe
                ]);

                exit;
            }

        } while ($hasMorePages);

        curl_close($curl);

        if( $companyData && $departmentData && $existingDepartmentsWithCompanies){

            // Fetch existing companies
            $existingCompanies = DB::connection('smAppTemplate')->table('wlsm_companies')
                ->whereIn('company_id', array_keys($companyData))
                ->pluck('company_name', 'company_id');

            // Update or insert companies
            foreach ($companyData as $companyID => $data) {
                if (isset($existingCompanies[$companyID])) {
                    // Update existing company
                    DB::connection('smAppTemplate')->table('wlsm_companies')
                        ->where('company_id', $companyID)
                        ->update($data);
                } else {
                    // Insert new company
                    DB::connection('smAppTemplate')->table('wlsm_companies')
                        ->insert($data);
                }
            }

            // Fetch existing departments
            $existingDepartments = DB::connection('smAppTemplate')->table('wlsm_departments')
                ->whereIn('department_id', array_keys($departmentData))
                ->pluck('department_description', 'department_id');

            // Update or insert departments
            foreach ($departmentData as $departmentID => $data) {
                if (isset($existingDepartments[$departmentID])) {
                    // Update existing department
                    DB::connection('smAppTemplate')->table('wlsm_departments')
                        ->where('department_id', $departmentID)
                        ->update($data);
                } else {
                    // Insert new department
                    DB::connection('smAppTemplate')->table('wlsm_departments')
                        ->insert($data);
                }
            }

            // Update the wlsm_departments table with the new company_ids
            foreach ($existingDepartmentsWithCompanies as $departmentID => $companyIdsString) {
                DB::connection('smAppTemplate')->table('wlsm_departments')
                    ->where('department_id', $departmentID)
                    ->update(['company_ids' => $companyIdsString]);
            }
        }

        $endTime = microtime(true);
        $elapsedTime = ($endTime - $startTime);
        $elapsedTime = $elapsedTime >= 60 ? numberFormat( ($elapsedTime / 60), 2)." minutes" : numberFormat($elapsedTime, 0)." seconds";

        DB::disconnect('smAppTemplate');

        return response()->json([
            'success' => true,
            'message' => 'Período <span class="text-success">'.date("m/Y", strtotime($meantime)).'</span> foi importado',
            'elapsed_time' => $elapsedTime,
            'slept' => $slept.' seconds',
            //'response' => json_encode($responseDataToMe)
        ]);
    }


    /**
     * Process API IPCA data from IBGE.
     * Get IPCA from IBGE and update smOnboard app_api
     * https://www.ibge.gov.br/explica/inflacao.php
     * https://servicodados.ibge.gov.br/api/docs/agregados?versao=3#api-bq
     * https://github.com/BrasilAPI/BrasilAPI/issues
     *
        ROTEIRO
        Query Builder
        Pesquisa
            - Índice Nacional de Preços ao Consumidor Amplo

        Agregado
        1737 - IPCA - Série histórica com número-índice, variação mensal e variações acumuladas em 3 meses, em 6 meses, no ano e em 12 meses (a partir de dezembro/1979)

        Variáveis
        2265 - IPCA - Variação acumulada em 12 meses

        Períodos 202306

        https://servicodados.ibge.gov.br/api/v3/agregados/1737/periodos/202306/variaveis/2265?localidades=N1[all]
    */

    public function updateIPCAfromIBGE()
    {
        $today = now()->toDateString();

        // Ensure that the updateIPCAfromIBGE function updates the data only once per day
        // Check if the record has already been updated today
        $existingRecord = DB::connection('smOnboard')->table('app_api')
            ->where('api_origin', 'ipca')
            ->whereDate('updated_at', $today)
            ->first();

        if ($existingRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Data has already been updated today'
            ]);
        }

        $periods = $this->generatePeriods();

        // Example:
        // https://servicodados.ibge.gov.br/api/v3/agregados/1737/periodos/202305|202306|202307|202308/variaveis/2265?localidades=N1[all]
        $API_query = 'https://servicodados.ibge.gov.br/api/v3/agregados/1737/periodos/' . join('|', $periods) . '/variaveis/2265?localidades=N1[all]';

        $response = Http::get($API_query);
        $data = $response->json();

        $ipcaData = $data[0]['resultados'][0]['series'][0]['serie'] ?? null;

        if (!empty($ipcaData) && is_array($ipcaData)) {
            $ipcaJson = json_encode($ipcaData);

            $record = DB::connection('smOnboard')->table('app_api')
                ->where('api_origin', 'ipca')
                ->first();

            if ($record) {
                // Update existing record
                DB::connection('smOnboard')->table('app_api')
                    ->where('api_origin', 'ipca')
                    ->update(['api_data' => $ipcaJson, 'updated_at' => now()]);
                $message = 'Data updated';
            } else {
                // Create new record
                DB::connection('smOnboard')->table('app_api')
                    ->insert(['api_origin' => 'ipca', 'api_data' => $ipcaJson, 'created_at' => now(), 'updated_at' => now()]);
                $message = 'Data created';
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No data retrieved from API or data format is incorrect'
            ]);
        }
    }

    private function generatePeriods()
    {
        $periods = [];
        $DateStart = date('Y', strtotime('1981-01'));
        $DateEnd = date('Y');

        foreach (range($DateStart, $DateEnd) as $aYear) {
            foreach (range(1, 12) as $aMonth) {
                $periods[] = date('Ym', strtotime($aYear . '-' . $aMonth));
            }
        }

        return array_unique(array_filter($periods));
    }




}
