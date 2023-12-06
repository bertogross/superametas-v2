<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OnboardController extends Controller
{

    // Get the password of a user from other smApp databases
    protected function getPasswordFromOtherDatabases($email)
    {
        // Get the list of other smApp databases from smOnboard
        $otherDatabases = $this->getOtherDatabases($email);

        foreach ($otherDatabases as $databaseName) {
            // Skip the current database
            if ($databaseName == config('database.connections.smAppTemplate.database')) {
                continue;
            }

            // Set the database connection configuration for the other database
            config([
                'database.connections.otherDatabase' => [
                    'driver' => 'mysql',
                    'host' => env('DB_HOST'),
                    'port' => env('DB_PORT'),
                    'database' => $databaseName,
                    'username' => env('DB_USERNAME'),
                    'password' => env('DB_PASSWORD'),
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'strict' => true,
                    'engine' => null,
                ],
            ]);

            // Check if the user exists in the other database
            $user = DB::connection('otherDatabase')
                ->table('users')
                ->where('email', $email)
                ->where('status', 1)
                ->first();

            if ($user) {
                // Return the user's password
                return $user->password;
            }
            // Disconnect from the other database
            DB::disconnect('otherDatabase');
        }

        // User not found in other databases
        return null;
    }

    // Update/Create subusers in smOnboard
    protected function updateOrCreateSubUser($email)
    {
        $databaseConnection = config('database.connections.smAppTemplate.database');

        // Get the ID of the current database connection. It is a helper function
        $databaseId = extractDatabaseId($databaseConnection);

        // Update or create a record in app_subusers
        $onboardConnection = DB::connection('smOnboard');
        $subuser = $onboardConnection->table('app_subusers')
            ->where('sub_user_email', $email)
            ->first();

        if ($subuser) {
            // Update the app_IDs column to include the new app_ID
            $appIds = json_decode($subuser->app_IDs, true);
            if (!in_array($databaseId, $appIds)) {
                $appIds[] = $databaseId;
                $onboardConnection->table('app_subusers')
                    ->where('sub_user_email', $email)
                    ->update(['app_IDs' => json_encode($appIds)]);
            }
        } else {
            // Create a new record with the given email and app_ID
            $appIds = array($databaseId);
            $onboardConnection->table('app_subusers')
                ->insert([
                    'sub_user_email' => $email,
                    'app_IDs' => json_encode($appIds),
                ]);
        }
    }

    // Get the list of other smApp databases from smOnboard
    public static function getOtherDatabases($email)
    {
        if (!$email) {
            return null;
        }

        $OnboardConnection = DB::connection('smOnboard');

        // Initialize an array to store other databases
        $otherDatabases = [];

        // Get the list of database names from app_users
        $appUsersTable = $OnboardConnection->table('app_users')
            ->where('user_email', $email)
            ->get()
            ->toArray();

        if($appUsersTable){
            foreach ($appUsersTable as $appId) {
                $databaseId = $appId->ID;
                $customerName = OnboardController::getCustomerNameByDatabaseId($databaseId);

                $otherDatabases[] = [
                    'database' => 'smApp' . $databaseId,
                    'customer' => $customerName
                ];
            }
        }

        // Get the list of app_IDs from app_subusers where sub_user_email is the given email
        $appSubusersTable = $OnboardConnection->table('app_subusers')
            ->where('sub_user_email', $email)
            ->get()
            ->toArray();

        if($appSubusersTable){
            foreach ($appSubusersTable as $appId) {
                $decoded = json_decode($appId->app_IDs, true);
                foreach($decoded as $id){
                    $databaseId = $id;
                    $customerName = OnboardController::getCustomerNameByDatabaseId($id);

                    $otherDatabases[] = [
                        'database' => 'smApp' . $databaseId,
                        'customer' => $customerName
                    ];
                }
            }
        }

        $allDatabaseNames = array_filter($otherDatabases);

        return $allDatabaseNames;
    }


    public static function getCustomerNameByDatabaseId($databaseId)
    {
        if(!$databaseId){
            return;
        }

        $databaseId = onlyNumber($databaseId);

        $OnboardConnection = DB::connection('smOnboard');

        // Get the list of database names from app_users
        $appUsersTable = $OnboardConnection->table('app_users')
            ->where('ID', $databaseId)
            ->first();

        return $appUsersTable->user_display_name;
    }

}
