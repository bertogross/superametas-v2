<?php

use App\Models\UserMeta;
use Illuminate\Support\Facades\DB;


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
        $phoneNumber = preg_replace('/\D/', '', $phoneNumber);

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


if (!function_exists('extractDatabaseId')) {
    function extractDatabaseId($databaseConnection)
    {
        // Logic to get the ID of the current database connection
        // This depends on how you have structured your database names and IDs
        // If your database names are smApp1, smApp2, etc., and IDs are 1, 2, etc.
        return intval(preg_replace('/\D/', '', $databaseConnection));
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
    function formatBrazilianReal(float $number): string {
        return 'R$ ' . number_format(
            number: $number,
            decimals: 2,
            decimal_separator: ',',
            thousands_separator: '.'
        );
    }
}


if (!function_exists('getCompanyAlias')) {
    /**
     * Get the company alias based on the company ID.
     *
     * @param int $companyId The ID of the company.
     * @return string|null The company alias or null if not found.
     */
    function getCompanyAlias(int $companyId): ?string {
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
    function getDepartmentAlias(int $departmentId): ?string {
        $departmentAlias = DB::connection('smAppTemplate')
            ->table('wlsm_departments') // replace with your departments table name
            ->where('department_id', $departmentId)
            ->value('department_alias'); // replace with the column name that stores the department alias

        return $departmentAlias ?: null;
    }
}


if (!function_exists('metricGoalSales')) {
    function metricGoalSales($meantime = false){
        $days_in_month = intval(date('t'));

        $metric = empty($meantime) || $meantime == date('Y-m') ? number_format( ( ( 100/$days_in_month ) * intval(date('d') ) ), 1, '.', '') : 100;

        return $metric . '%';
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

        $AuthorizedCompanies = $AuthorizedCompanies ? json_decode($AuthorizedCompanies, true) : '';

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


function onlyNumber($goalData = null) {
    return $goalData ? preg_replace('/\D/', '', $goalData) : 0;
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



if(!function_exists('APP_print_r')){
	function APP_print_r($data){
		if( !empty($data) ){
			print '<pre>';
				print_r( $data );
			print '</pre>';
		}
	}
}
