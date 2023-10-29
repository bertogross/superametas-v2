<?php

use App\Models\User;
use App\Models\UserMeta;
use Illuminate\Support\Facades\DB;


if (!function_exists('getUsers')) {
    /**
     * Get all users with status = 1, ordered by name.
     *
     * @return \Illuminate\Support\Collection
     */
    function getUsers() {
        return DB::connection('smAppTemplate')
            ->table('users')
            ->where('status', 1)
            ->orderBy('name')
            ->get();
    }
}

if (!function_exists('getUsersByRole')) {
    function getUsersByRole($role)
    {
        return DB::connection('smAppTemplate')
            ->table('users')
            ->where('role', $role)
            ->where('status', 1) // Assuming you want to get only active users
            ->orderBy('name')
            ->get();
    }
}


if( !function_exists('getUserData') ){
    function getUserData($userId = null) {
        $user = $userId ? User::find($userId) : Auth::user();

        if ($user) {
            return array(
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
                'avatar' => $user->avatar,
                'cover' => $user->cover,
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
            );
        }

        return null;
    }
}

if (!function_exists('canManageGoalSales')) {
    function canManageGoalSales() {
        $user = auth()->user();
        return $user && $user->hasAnyRole(User::ROLE_ADMIN, User::ROLE_EDITOR) && request()->is('goal-sales');
    }
}


// Retrieve a user's meta value based on the given key.
if (!function_exists('getUserMeta')) {
    /**
     * @param int    $userId The ID of the user.
     * @param string $key    The meta key to retrieve.
     * @return mixed The meta value or null if not found.
     */
    function getUserMeta($userId, $key)
    {
        return UserMeta::getUserMeta($userId, $key);
    }
}


// Format a phone number to the pattern (XX) X XXXX-XXXX.
if (!function_exists('formatPhoneNumber')) {
    /**
     * @param string $phoneNumber The phone number to be formatted.
     * @return string The formatted phone number or an empty string if the input is empty.
     */
    function formatPhoneNumber($phoneNumber) {
        // Remove all non-numeric characters from the phone number.
        $phoneNumber = onlyNumber($phoneNumber);

        // Apply the desired formatting pattern to the phone number.
        return !empty($phoneNumber) ? preg_replace('/(\d{2})(\d{1})(\d{4})(\d{4})/', '($1) $2 $3-$4', $phoneNumber) : '';
    }
}


// Get ERP data
if (!function_exists('getERPdata')) {
    function getERPdata($databaseConnection) {
        // Get smAppTemplate connection databse name
        $databaseName = config('database.connections.smAppTemplate.database');
        $databaseId = !empty($databaseConnection) ? intval($databaseConnection) : extractDatabaseId($databaseName);

        // Set the database connection to smOnboard
        $OnboardConnection = DB::connection('smOnboard');

        // Fetch user_erp_data where ID is 1
        $user_erp_data = $OnboardConnection->table('app_users')->where('ID', $databaseId)->value('user_erp_data');

        if ($user_erp_data) {
            $ERPdata = json_decode($user_erp_data, true);

            // Extract the required values
            $customer = $ERPdata['api']['customer'];
            $username = $ERPdata['api']['username'];
            $password = $ERPdata['api']['password'];
            $term = $ERPdata['api']['term'];

            return [
                'customer' => $customer,
                'username' => $username,
                'password' => $password,
                'term' => $term
            ];
        } else {
            return null;
        }

        return view('settings/database', [
            'onboard_id' => $customer,
            'target' => 'sales',
            'key' => 'ffs64DSA2ds4',
            'meantime' => date('Y-m'),
        ]);
    }
}

if (!function_exists('getIPCAdata')) {
    function getIPCAdata($meantime)
    {
        $return = '';

        try {
            $OnboardConnection = DB::connection('smOnboard');
            $result = $OnboardConnection->table('app_api')->where('api_origin', 'ipca')->first();

            if (!$result) {
                return '';
            }

            $queryResult = isset($result->api_data) ? json_decode($result->api_data, true) : '';

            if (!empty($queryResult)) {
                if (!empty($queryResult[onlyNumber($meantime)]) && !empty($meantime)) {
                    $return = array(
                        'period' => $meantime,
                        'value' => $queryResult[onlyNumber($meantime)]
                    );
                } else {
                    end($queryResult);
                    $lastKey = key($queryResult);

                    $date = substr($lastKey, 0, -2) . '-' . substr($lastKey, -2, 6);
                    $return = isset($queryResult[$lastKey]) ? array(
                        'period' => date('Y-m', strtotime($date)),
                        'value' => $queryResult[$lastKey]
                    ) : '';
                }
            }
        } catch (\Exception $e) {
            // Log the error message
            \Log::error($e->getMessage(), ['method' => __METHOD__, 'file' => __FILE__, 'line' => $e->getLine()]);

            return '';
        }

        return $return;
    }
}

if (!function_exists('extractDatabaseId')) {
    function extractDatabaseId($databaseConnection)
    {
        // Logic to get the ID of the current database connection
        // This depends on how you have structured your database names and IDs
        // If your database names are smApp1, smApp2, etc., and IDs are 1, 2, etc.
        return onlyNumber($databaseConnection);
    }
}


if (!function_exists('getOtherDatabases')) {
    /**
     * Get the list of other smApp databases from smOnboard.
     *
     * @return array The list of other smApp databases.
     */
    function getOtherDatabases($email)
    {
        $OnboardConnection = DB::connection('smOnboard');

        $otherDatabases = [];

        // Get the list of database names from app_users
        $app_usersTable = $OnboardConnection->table('app_users')
            ->where('user_email', $email)
            ->value('ID');

        // Add the database names to the list of other databases
        if ($app_usersTable) {
            $otherDatabases[] = 'smApp' . $app_usersTable;
        }

        // Get the list of app_IDs from app_subusers where sub_user_email is the given email
        $app_subusersTable = $OnboardConnection->table('app_subusers')
            ->where('sub_user_email', $email)
            ->value('app_IDs');

        // Convert the app_IDs from JSON to array
        if ($app_subusersTable) {
            $appIds = json_decode($app_subusersTable, true);
            foreach ($appIds as $appId) {
                $otherDatabases[] = 'smApp' . $appId;
            }
        }

        // Remove duplicates and filter out empty values
        $otherDatabases = array_unique($otherDatabases);
        $otherDatabases = array_filter($otherDatabases);

        return $otherDatabases;
    }
}


if (!function_exists('formatBrazilianReal')) {
    /**
     * Format a number as Brazilian Real.
     *
     * @param float $number The number to be formatted.
     * @return string The formatted number.
     */
    function formatBrazilianReal($number, $decimalPlaces = 2): string {
        return !empty($number) && intval($number) > 0 ? 'R$ ' . numberFormat( $number, $decimalPlaces ) : 'R$ 0,00';
    }
}


if (!function_exists('getCompanyAlias')) {
    /**
     * Get the company alias based on the company ID.
     *
     * @param int $companyId The ID of the company.
     * @return string|null The company alias or null if not found.
     */
    function getCompanyAlias($companyId){
        $companyId = intval($companyId);

        $companyAlias = DB::connection('smAppTemplate')
            ->table('wlsm_companies') // replace with your companies table name
            ->where('company_id', $companyId)
            ->value('company_alias'); // replace with the column name that stores the company alias

        return $companyAlias ?: null;
    }
}


if (!function_exists('getDepartmentAlias')) {
    /**
     * Get the department alias based on the department ID.
     *
     * @param int $departmentId The ID of the department.
     * @return string|null The department alias or null if not found.
     */
    function getDepartmentAlias($departmentId){
        $departmentId = intval($departmentId);

        $departmentAlias = DB::connection('smAppTemplate')
            ->table('wlsm_departments') // replace with your departments table name
            ->where('department_id', $departmentId)
            ->value('department_alias'); // replace with the column name that stores the department alias

        return $departmentAlias ?: null;
    }
}


if (!function_exists('metricGoalSales')) {
    function metricGoalSales($meantime = null){
        $daysInMonth = intval(date('t'));

        if( empty($meantime) || $meantime == date('Y-m') ){
            $metric = numberFormat( ( ( 100/$daysInMonth ) * intval(date('d') ) ), 1);
        } elseif (is_string($meantime) && strtotime($meantime) !== false && date('Y-m', strtotime($meantime)) < date('Y-m')) {
            $metric = 100;
        } else{
            $metric = 100;
        }

        return $metric;
    }
}


if (!function_exists('getActiveCompanies')) {
    /**
     * Get active companies from the wlsm_companies table.
     */
    function getActiveCompanies() {
        return DB::connection('smAppTemplate')
            ->table('wlsm_companies')
            ->where('status', 1)
            ->orderBy('company_id')
            ->get();
    }
}


if (!function_exists('getAuthorizedCompanies')) {
    /**
     * Get authorized companies from the user_metas table
     */
    function getAuthorizedCompanies($userId = null) {

        $userId = $userId ?? Auth::user()->id;

        $AuthorizedCompanies = getUserMeta($userId, 'companies');

        $AuthorizedCompanies = $AuthorizedCompanies ? json_decode($AuthorizedCompanies, true) : array();

        return empty($AuthorizedCompanies) ? getActiveCompanies() : $AuthorizedCompanies;
    }
}


if (!function_exists('getActiveDepartments')) {
    /**
     * Get active departments from the wlsm_departments table.
     */
    function getActiveDepartments() {
        return DB::connection('smAppTemplate')
        ->table('wlsm_departments')
            ->where('status', 1)
            ->orderBy('department_alias')
            ->get();
    }
}


if (!function_exists('getSettings')) {
    function getSettings($key) {
        if(!empty($key)){
            $settings = DB::connection('smAppTemplate')->table('settings')->pluck('value', 'key')->toArray();
            return isset($settings[$key]) ? $settings[$key] : '';
        }
    }
}

if (!function_exists('getCompanyLogo')) {
    function getCompanyLogo(){
        return URL::asset('storage/' . getSettings('logo'));
    }
}

if (!function_exists('getCompanyName')) {
    function getCompanyName(){
        return getSettings('name');
    }
}

if (!function_exists('getGoogleToken')) {
    function getGoogleToken(){
        return getSettings('google_token');
    }
}

if (!function_exists('getDropboxToken')) {
    function getDropboxToken(){
        return getSettings('dropbox_token');
    }
}

if (!function_exists('formatSize')) {
    function formatSize($size) {
        $base = log($size, 1024);
        $suffixes = array('Bytes', 'KB', 'MB', 'GB', 'TB');

        return !empty(onlyNumber($size)) ? round(pow(1024, $base - floor($base)), 2) . ''.$suffixes[floor($base)].'' : 0;
    }
}



if (!function_exists('onlyNumber')) {
    function onlyNumber($number = null) {
        $numericValue = preg_replace('/\D/', '', $number);
        return is_numeric($numericValue) ? intval($numericValue) : 0;
    }
}

if (!function_exists('numberFormat')) {
    function numberFormat($number, $decimalPlaces = 0) {
        $numericValue = is_numeric($number) ? floatval($number) : 0;
        return number_format($numericValue, $decimalPlaces, ',', '.');
    }
}

if (!function_exists('convertToNumeric')) {
    function convertToNumeric($number) {
        return floatval(str_replace(',', '.', str_replace('.', '', $number)));
    }
}

if (!function_exists('getSaleDateRange')) {
    function getSaleDateRange()
    {
        $firstDate = DB::connection('smAppTemplate')->table('wlsm_sales')
            ->select(DB::raw('DATE_FORMAT(MIN(date_sale), "%Y-%m") as first_date'))
            ->first();

        $lastDate = DB::connection('smAppTemplate')->table('wlsm_sales')
            ->select(DB::raw('DATE_FORMAT(MAX(date_sale), "%Y-%m") as last_date'))
            ->first();

        return [
            'first_date' => $firstDate->first_date,
            'last_date' => $lastDate->last_date,
        ];
    }
}


if (!function_exists('getLastUpdate')) {
    function getLastUpdate($tableName, $dateFormat = "Y-m-d") {
        try {
            // Get the last updated date from the table
            $lastUpdate = DB::connection('smAppTemplate')
                ->table($tableName)
                ->orderBy('updated_at', 'desc')
                ->limit(1)
                ->value('updated_at');

            // Check if there is a result
            if ($lastUpdate) {
                // Format the date and return it
                return date($dateFormat, strtotime($lastUpdate));
            } else {
                return '';
            }
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
}



if (!function_exists('getGoalsId')) {
    function getGoalsId($companyId, $meantime, $type) {
        try {
            $goal = DB::connection('smAppTemplate')
                ->table('wlsm_goals')
                ->where('company_id', $companyId)
                ->where('meantime', $meantime)
                ->where('type', $type)
                ->first();

            return $goal ? $goal->id : null;
        } catch (\Exception $e) {
            \Log::error('Failed to get goal sales post ID: ' . $e->getMessage());
            return false;
        }
        return false;
    }
}


if( !function_exists('goalsEmojiChart') ){
	function goalsEmojiChart($nChartId, $goal, $sale, $departmentId, $departmentName, $companyName, $percent, $percentAccrued, $style = ''){
		$html = '';

		$bsTitle = !empty($companyName) ? $companyName.' :: '.$departmentName : ':: '.$departmentName;

		if( $goal == 0 && $sale > 0 && $departmentId != 'general' && ( auth()->user()->hasAnyRole(User::ROLE_ADMIN, User::ROLE_EDITOR) ) ){
			$html .= '<i class="text-danger blink ri-error-warning-line fw-bold position-relatvie w-auto mx-auto" data-bs-toggle="tooltip" data-bs-placement="top" title="Existe um conflito entre valor de Meta e Vendas para departamento '.$departmentName.'. Não há Meta ou não deveria haver vendas para este departamento no estabelecimento '.$companyName.'" style="z-index:2;"></i>';
		}

		$html .= '<div id="goal-chart-'.$nChartId.'" class="goal-chart text-center d-inline-block" ';
            if( auth()->user()->hasAnyRole(User::ROLE_ADMIN, User::ROLE_EDITOR) ) {
                $html .=  'data-sale="'.$sale.'" data-goal="'.$goal.'" ';
            }
		    $html .= 'data-percent="'.$percent.'" data-percent-from-metric="'.$percentAccrued.'" data-department-name="'.$departmentName.'" data-department="'.$departmentId.'" ';
			if( auth()->user()->hasAnyRole(User::ROLE_ADMIN, User::ROLE_EDITOR) ) {
				$html .= 'data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="hover focus" data-bs-title="'.$bsTitle.'" data-bs-content="';
				$html .= "<i class='text-theme ri-checkbox-blank-circle-fill align-bottom me-1'></i>Vendas: ".formatBrazilianReal($sale, 0)."<br>";

				$html .= "<i class='text-info ri-checkbox-blank-circle-fill align-bottom me-1'></i>Meta: ".formatBrazilianReal($goal, 0)."";

				$html .= '" data-bs-html="true" ';
			}
			$html .= 'data-style="'.$style.'" dir="ltr">';
		$html .= '</div>';

		return $html;
	}
}




if (!function_exists('getCookie')) {
    /**
     * Get the value of a specific cookie by its name.
     *
     * @param string $cookieName The name of the cookie.
     * @return mixed The value of the cookie or null if the cookie does not exist.
     */
    function getCookie($cookieName) {
        $cookieValue = request()->cookie($cookieName);
        //dd($cookieValue);
        return $cookieValue;
    }
}





if(!function_exists('APP_print_r')){
	function APP_print_r($data){
		if( !empty($data) ){
			print '<pre>';
				print_r( $data );
			print '</pre>';
		}
	}
}
