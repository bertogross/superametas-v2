<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cookie;
//use Illuminate\Support\Facades\Session;

class SetDynamicDatabase
{
    public function handle($request, Closure $next)
    {
        // Retrieve user by email from smOnboard
        $user = DB::connection('smOnboard')->table('app_users')->where('user_email', $request->email)->first();

        if (!$user) {
            // Handle invalid user
            return redirect()->back()->withErrors(['email' => 'User email not found']);
        }

        // Determine which database to connect to based on user ID
        $dynamicDatabaseName = isset($user->ID) ? 'smApp' . $user->ID : '';

        if( empty($dynamicDatabaseName ) ){
            return redirect()->back()->withErrors(['database' => 'Database not found']);
        }

        $dynamicDatabaseNameEncrypted = !empty($dynamicDatabaseName) ? Crypt::encryptString($dynamicDatabaseName) : '';

        // Set the dynamic database connection configurations
        Config::set('database.connections.smAppTemplate.database', $dynamicDatabaseName);

        // SM-DBN store the client Supera Metas DataBase Name
        //Cookie::queue('SM-DBN', $dynamicDatabaseNameEncrypted, 60 * 24 * 360);

        //Cookie::queue('SM-DBN', $dynamicDatabaseName, 60 * 24 * 360);
        //setcookie("SM-DBN", $dynamicDatabaseNameEncrypted, time()+(3600 * 24 * 360));
        setcookie("SM-DBN", $dynamicDatabaseName, time()+(3600 * 24 * 360));

        //Session::put('SM-DBN', $dynamicDatabaseNameEncrypted);
        //Session::save();

        // Make sure to use the new database connection
        DB::purge('smAppTemplate');

        return $next($request);
    }
}
