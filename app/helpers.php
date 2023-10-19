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
function getERPdata($databaseConnection) {
    // Get smAppTemplate connection databse name
    $databaseName = config('database.connections.smAppTemplate.database');
    $smAppID = !empty($databaseConnection) ? intval($databaseConnection) : intval(preg_replace('/\D/', '',$databaseName));

    // Set the database connection to smOnboard
    $OnboardConnection = DB::connection('smOnboard');

    // Fetch user_erp_data where ID is 1
    $user_erp_data = $OnboardConnection->table('app_users')->where('ID', $smAppID)->value('user_erp_data');

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

    return view('settings-database', [
        'onboard_id' => $customer,
        'target' => 'sales',
        'key' => 'ffs64DSA2ds4',
        'meantime' => date('Y-m'),
    ]);
}
