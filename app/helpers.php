<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\Survey;
use App\Models\UserMeta;
use App\Models\SurveyTerms;
use App\Models\SurveyTopic;
use App\Models\SurveyResponse;
use App\Models\SurveyTemplates;
use App\Models\SurveyAssignments;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\SettingsStripeController;

function appName(){
    $host = $_SERVER['HTTP_HOST'] ?? 'default';
    return str_contains($host, 'testing') ? 'Checklist' : env('APP_NAME');
}

// Get all users with status = 1, ordered by name
if (!function_exists('getUsers')) {
    function getUsers() {
        $getUsers = DB::connection('smAppTemplate')
            ->table('users')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        $getUsers = $getUsers ?? null;
        return is_string($getUsers) ? json_decode($getUsers, true) : $getUsers;
    }
}

if (!function_exists('getUsersByRole')) {
    function getUsersByRole($role){
        if($role){
            $getUsersByRole = DB::connection('smAppTemplate')
                ->table('users')
                ->where('role', $role)
                ->where('status', 1) // Assuming you want to get only active users
                ->orderBy('name')
                ->get();

            $getUsersByRole = $getUsersByRole ?? null;
            return is_string($getUsersByRole) ? json_decode($getUsersByRole, true) : $getUsersByRole;
        }

        return null;
    }
}

if( !function_exists('getUserData') ){
    function getUserData($userId = null) {
        $user = $userId ? User::find($userId) : auth()->user();

        if ($user) {
            return array(
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
                'avatar' => $user->avatar ? URL::asset('storage/' . $user->avatar) : URL::asset('build/images/users/user-dummy-img.jpg'),
                'cover' => $user->cover ? URL::asset('storage/' . $user->cover) : URL::asset('build/images/small/img-9.jpg'),
                'role' => $user->role,
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
    function getUserMeta($userId, $key) {
        return UserMeta::getUserMeta($userId, $key);
    }
}

// Format a phone number to the pattern (XX) X XXXX-XXXX.
if (!function_exists('formatPhoneNumber')) {
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

    }
}

if (!function_exists('getERP')) {
    function getERP(){
        $databaseName = config('database.connections.smAppTemplate.database');
        $databaseId = !empty($databaseConnection) ? intval($databaseConnection) : extractDatabaseId($databaseName);

        // Set the database connection to smOnboard
        $OnboardConnection = DB::connection('smOnboard');

        return $OnboardConnection->table('app_users')->where('ID', $databaseId)->value('user_erp') ?? null;
    }
}

// Get ERP data
if (!function_exists('getStripeData')) {
    function getStripeData() {
        $databaseName = config('database.connections.smAppTemplate.database');
        $databaseId = !empty($databaseConnection) ? intval($databaseConnection) : extractDatabaseId($databaseName);

        // Set the database connection to smOnboard
        $OnboardConnection = DB::connection('smOnboard');

        // Fetch user_erp_data where ID is $databaseId
        $userData = $OnboardConnection->table('app_users')
            ->where('ID', $databaseId)
            ->select([
                'user_stripe_customer_id',
                'user_stripe_products',
                'user_stripe_subscription_id',
                'user_stripe_subscription_status',
                'user_stripe_subscription_quantity'
            ])
            ->first();

        if ($userData) {
            // Map the fetched data to the $stripeData array
            $stripeData = [
                'customer_id' => $userData->user_stripe_customer_id,
                'products' => $userData->user_stripe_products,
                'subscription_id' => $userData->user_stripe_subscription_id,
                'subscription_status' => $userData->user_stripe_subscription_status,
                'subscription_quantity' => $userData->user_stripe_subscription_quantity,
            ];

            return $stripeData;
        }

        return null;
    }
}

if (!function_exists('subscriptionLabel')) {
    function subscriptionLabel(){
        $stripeData = getStripeData();
        $subscriptionId = $stripeData['subscription_id'];
        $subscriptionStatus = $stripeData['subscription_status'];

        $status_translated = SettingsStripeController::subscriptionStatusTranslation($subscriptionStatus);

        $label = $status_translated['label'];
        $description = $status_translated['description'];
        $color = $status_translated['color'];
        $class = $status_translated['class'];

        print Auth::user()->hasRole(User::ROLE_ADMIN) ? '<span class="badge bg-transparent border border-'.$color.' text-'.$color.' float-end text-decoration-none fw-normal small '.$class.'" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-title="'.$label.'" data-bs-content="'.$description.'">'.$label.'</span>' : '';
    }
}

// Logic to get the ID of the current database connection
if (!function_exists('extractDatabaseId')) {
    function extractDatabaseId($databaseConnection) {
        // This depends on how you have structured your database names and IDs
        // If your database names are smApp1, smApp2, etc., and IDs are 1, 2, etc.
        return onlyNumber($databaseConnection);
    }
}

// Get IPCA data
if (!function_exists('getIPCAdata')) {
    function getIPCAdata($meantime) {
        try {
            $OnboardConnection = DB::connection('smOnboard');
            $result = $OnboardConnection->table('app_api')->where('api_origin', 'ipca')->first();

            if (!$result) {
                return '';
            }

            $queryResult = isset($result->api_data) ? json_decode($result->api_data, true) : '';

            if (!empty($queryResult)) {
                if (!empty($queryResult[onlyNumber($meantime)]) && !empty($meantime)) {
                    return array(
                        'period' => $meantime,
                        'value' => $queryResult[onlyNumber($meantime)]
                    );
                } else {
                    end($queryResult);
                    $lastKey = key($queryResult);

                    $date = substr($lastKey, 0, -2) . '-' . substr($lastKey, -2, 6);
                    return isset($queryResult[$lastKey]) ? array(
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

        return null;
    }
}

// Format a number as Brazilian Real
if (!function_exists('brazilianRealFormat')) {
    function brazilianRealFormat($number, $decimalPlaces = 2): string {
        return !empty($number) && intval($number) > 0 ? 'R$ ' . numberFormat( $number, $decimalPlaces ) : 'R$ 0,00';
    }
}

// Get authorized companies from the user_metas table
if (!function_exists('getAuthorizedCompanies')) {
    function getAuthorizedCompanies($userId = null) {
        $userId = $userId ?? auth()->id();

        $AuthorizedCompanies = getUserMeta($userId, 'companies');

        $AuthorizedCompanies = !empty($AuthorizedCompanies) ? json_decode($AuthorizedCompanies, true) : array();

        return empty($AuthorizedCompanies) ? [1] : $AuthorizedCompanies;
    }
}

// Get active companies from the wlsm_companies table.
if (!function_exists('getActiveCompanies')) {
    function getActiveCompanies() {
        $activeCompanies = DB::connection('smAppTemplate')
            ->table('wlsm_companies')
            ->where('status', 1)
            ->orderBy('company_id')
            ->get()
            ->toArray();

        return $activeCompanies ?? null;
    }
}

// Get the company alias based on the company ID
if (!function_exists('getCompanyNameById')) {
    function getCompanyNameById($companyId){
        if($companyId){
            $companyId = intval($companyId);

            $companyAlias = DB::connection('smAppTemplate')
                ->table('wlsm_companies')
                ->where('company_id', $companyId)
                ->value('company_alias');

            return $companyAlias ?: null;
        }
        return null;
    }
}

// Get active departments from the wlsm_departments table.
if (!function_exists('getActiveDepartments')) {
    function getActiveDepartments() {
        $getActiveDepartments = DB::connection('smAppTemplate')
        ->table('wlsm_departments')
            ->where('status', 1)
            ->orderBy('department_alias')
            ->get();

        $getActiveDepartments = $getActiveDepartments ?? null;
        return is_string($getActiveDepartments) ? json_decode($getActiveDepartments, true) : $getActiveDepartments;
    }
}

// Get active wharehouse terms.
if (!function_exists('getWarehouseTerms')) {
    function getWarehouseTerms() {
        $data = [];

        // Set the database connection to smWarehouse
        $warehouseConnection = DB::connection('smWarehouse');

        // Fetch terms with their topics
        $terms = $warehouseConnection->table('survey_terms')
            ->where('survey_terms.status', 1)
            ->join('survey_topics', 'survey_terms.id', '=', 'survey_topics.term_id')
            ->orderBy('survey_terms.term_order')
            ->orderBy('survey_topics.topic_order')
            ->select(
                'survey_terms.id AS term_id',
                'survey_terms.name AS term_name',
                'survey_terms.term_order AS term_order',
                'survey_topics.id AS topic_id',
                'survey_topics.question AS topic_question',
                'survey_topics.topic_order AS topic_order',
            )
            ->get();

        // Group topics by term
        foreach ($terms as $term) {
            if (!isset($data[$term->term_id])) {
                $data[$term->term_id] = [
                    'stepData' => [
                        'term_id' => $term->term_id,
                        'term_name' => $term->term_name,
                        'type' => 'default',
                        'original_position' => $term->term_order,
                        'new_position' => $term->term_order,
                    ],
                    'topics' => []
                ];
            }

            $data[$term->term_id]['topics'][] = [
                'topic_id' => $term->topic_id,
                'question' => $term->topic_question,
                'original_position' => $term->topic_order,
                'new_position' => $term->topic_order,
            ];
        }

        // Optional: Re-index array to remove term_id keys
        $data = array_values($data);

        return $data;
    }

}

// Get the department alias based on the department ID
if (!function_exists('getDepartmentNameById')) {
    function getDepartmentNameById($departmentId){
        $departmentId = intval($departmentId);

        $departmentAlias = DB::connection('smAppTemplate')
            ->table('wlsm_departments') // replace with your departments table name
            ->where('department_id', $departmentId)
            ->value('department_alias');

        return $departmentAlias ?: null;
    }
}

// Get the term name based on the ID
if (!function_exists('getWarehouseTermNameById')) {
    function getWarehouseTermNameById($termId){
        $termId = intval($termId);

        $termName = DB::connection('smWarehouse')
            ->table('survey_terms')
            ->where('id', $termId)
            ->value('name');

        return $termName ?: null;
    }
}

if (!function_exists('metricGoalSales')) {
    function metricGoalSales($meantime = null){
        $daysInMonth = intval(date('t'));

        $metric = 100;

        if( empty($meantime) || $meantime == date('Y-m') ){
            $metric = numberFormat( ( ( 100/$daysInMonth ) * intval(date('d') ) ), 1);
        } elseif (is_string($meantime) && strtotime($meantime) !== false && date('Y-m', strtotime($meantime)) < date('Y-m')) {
            $metric = 100;
        }

        return $metric;
    }
}

if (!function_exists('getSettings')) {
    function getSettings($key) {
        // Static variable to hold the settings array
        static $settingsCache = null;

        // Check if settings have already been loaded
        if ($settingsCache === null) {
            // Load settings and cache them
            $settingsCache = DB::connection('smAppTemplate')->table('settings')->pluck('value', 'key')->toArray();
        }

        // Return the setting if it exists, or an empty string as a default
        return isset($settingsCache[$key]) ? $settingsCache[$key] : '';
    }
}

if (!function_exists('getCompanyLogo')) {
    function getCompanyLogo(){
        // Use the 'getSettings' function which uses a cached version of settings
        return getSettings('logo') ? URL::asset('storage/' . getSettings('logo')) : null;
    }
}

if (!function_exists('getCompanyName')) {
    function getCompanyName(){
        // Use the 'getSettings' function which uses a cached version of settings
        return getSettings('name');
    }
}

if (!function_exists('getGoogleToken')) {
    function getGoogleToken(){
        // Use the 'getSettings' function which uses a cached version of settings
        return getSettings('google_token');
    }
}

if (!function_exists('getDropboxToken')) {
    function getDropboxToken(){
        // Use the 'getSettings' function which uses a cached version of settings
        return getSettings('dropbox_token');
    }
}

if (!function_exists('formatSize')) {
    function formatSize($size) {
        if($size){
            $base = log($size, 1024);
            $suffixes = array('Bytes', 'KB', 'MB', 'GB', 'TB');

            return !empty(onlyNumber($size)) ? round(pow(1024, $base - floor($base)), 2) . ''.$suffixes[floor($base)].'' : 0;
        }
        return 0;
    }
}

if (!function_exists('statusBadge')) {
    function statusBadge($status) {
        switch ($status) {
            case 'active':
                return '<span class="badge bg-success-subtle text-success text-uppercase" title="Registro de Status Ativo">Ativo</span>';
            case 'trash':
                return '<span class="badge bg-danger text-theme text-uppercase" title="Registro de Status Deletado">Deletado</span>';
            case 'disabled':
                return '<span class="badge bg-danger-subtle text-danger text-uppercase" title="Registro de Status Desativado">Desativado</span>';
            default:
                return '';
        }
    }
}

if (!function_exists('onlyNumber')) {
    function onlyNumber($number = null) {
        if($number){
            $numericValue = preg_replace('/\D/', '', $number);
            return is_numeric($numericValue) ? intval($numericValue) : 0;
        }
        return 0;
    }
}

if (!function_exists('numberFormat')) {
    function numberFormat($number, $decimalPlaces = 0) {
        if($number){
            $numericValue = is_numeric($number) ? floatval($number) : 0;
            return number_format($numericValue, $decimalPlaces, ',', '.');
        }
        return 0;
    }
}

if (!function_exists('convertToNumeric')) {
    function convertToNumeric($number) {
        if($number){
            return floatval(str_replace(',', '.', str_replace('.', '', $number)));
        }
        return 0;
    }
}

if (!function_exists('getLastSalesUpdate')) {
    function getLastSalesUpdate($tableName, $dateFormat = "Y-m-d") {
        try {
            // Get the last updated date from the table
            $lastUpdate = DB::connection('smAppTemplate')
                ->table($tableName)
                ->orderBy('created_at', 'desc')
                ->limit(1)
                ->value('created_at');

            // Check if there is a result
            if ($lastUpdate) {

                // Set the locale to Brazilian Portuguese
                \Carbon\Carbon::setLocale('pt_BR');

                // Parse the date using Carbon, format it, and return it
                return \Carbon\Carbon::parse($lastUpdate)->isoFormat('D [de] MMMM, YYYY - HH:mm') . 'hs';

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

if (!function_exists('getTermNameById')) {
    function getTermNameById($termId) {
        $term = $termId ? SurveyTerms::find($termId) : null;

        return $term ? $term->name : null;
    }
}

if (!function_exists('getSurveyTemplateNameById')) {
    function getSurveyTemplateNameById($templateId) {
        $template = $templateId ? SurveyTemplates::find($templateId) : null;

        return $template ? $template->title : null;
    }
}


if (!function_exists('getTemplateDescriptionById')) {
    function getTemplateDescriptionById($templateId) {
        $template = $templateId ? SurveyTemplates::find($templateId) : null;

        return $template ? $template->description : null;
    }
}

if (!function_exists('getTemplateRecurringById')) {
    function getTemplateRecurringById($templateId) {
        $template = $templateId ? SurveyTemplates::find($templateId) : null;

        return $template ? $template->recurring : null;
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
				$html .= "<i class='text-theme ri-checkbox-blank-circle-fill align-bottom me-1'></i>Vendas: ".brazilianRealFormat($sale, 0)."<br>";

				$html .= "<i class='text-info ri-checkbox-blank-circle-fill align-bottom me-1'></i>Meta: ".brazilianRealFormat($goal, 0)."";

				$html .= '" data-bs-html="true" ';
			}
			$html .= 'data-style="'.$style.'" dir="ltr">';
		$html .= '</div>';

		return $html;
	}
}

// Get the value of a specific cookie by its name
if (!function_exists('getCookie')) {
    function getCookie($cookieName) {
        $cookieValue = request()->cookie($cookieName);
        //dd($cookieValue);
        return $cookieValue;
    }
}

//Max length (limit chars)
if (!function_exists('limitChars')) {
    function limitChars($text, $number = 50) {
        return $text ? \Illuminate\Support\Str::limit($text, $number) : '';
    }
}

// Get the progress bar class based on the completion percentage
if (!function_exists('getProgressBarClass')) {
    function getProgressBarClass($percentage){
        if ($percentage >= 100) {
            return 'success'; // Completed
        } elseif ($percentage > 75) {
            return 'info'; // High progress
        } elseif ($percentage > 50) {
            return 'primary'; // Moderate progress
        } elseif ($percentage > 25) {
            return 'warning'; // Low progress
        } else {
            return 'danger'; // Just started or no progress
        }
    }
}


//Useful to see on the bottom left side fixed smotth fixed div
if(!function_exists('appPrintR')){
	function appPrintR($data){
		/*if( !empty($data) ){
			print '<pre class="language-markup"><code>';
				print_r( $data );
			print '</code></pre>';
		}*/
        if( !empty($data) ){
			print '<pre>';
				print_r( $data );
			print '</pre>';
		}
	}
}

//Useful to print inside the content body
if(!function_exists('appPrintR2')){
	function appPrintR2($data){
		if( !empty($data) ){
			print '<pre class="language-markup" style="font-family: inherit; white-space: pre-wrap; color: #87DF01;">'.var_export( $data, true).'</pre>';
		}
	}
}
