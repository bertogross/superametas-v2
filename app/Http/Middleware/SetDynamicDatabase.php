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
        $email = $request->email;

        $email = filter_var($email, FILTER_VALIDATE_EMAIL);

        if (!$email) {
            // Handle invalid user
            return redirect()->back()->withErrors(['email' => 'Informe um endereço de e-mail válido']);
        }

        // Retrieve user by email from smOnboard
        $user = DB::connection('smOnboard')->table('app_users')->where('user_email', $email)->first();

        if (!$user) {
            // Handle invalid user
            return redirect()->back()->withErrors(['email' => 'Endereço de e-mail não cadastrado em nossa base de dados']);
        }

        // Determine which database to connect to based on user ID
        $dynamicDatabaseName = isset($user->ID) ? 'smApp' . $user->ID : '';

        if( empty($dynamicDatabaseName ) ){
            return redirect()->back()->withErrors(['database' => 'Database not found']);
        }

        // Set the dynamic database connection configurations
        Config::set('database.connections.smAppTemplate.database', $dynamicDatabaseName);

        $dynamicDatabaseNameEncrypted = !empty($dynamicDatabaseName) ? Crypt::encryptString($dynamicDatabaseName) : '';

        // SM-DBN store the client Supera Metas DataBase Name
        //Cookie::queue('SM-DBN', $dynamicDatabaseNameEncrypted, 60 * 24 * 360);
        //Cookie::queue('SM-DBN', $dynamicDatabaseName, 60 * 24 * 360);
        //setcookie("SM-DBN", $dynamicDatabaseNameEncrypted, time()+(3600 * 24 * 360));
        setcookie("SM-DBN", base64_encode('diI6IlZrNWllZmxSNXZ0WEJsNUpMM1cEtNa2VBMmhqQWErK0F0dkphRHhQemZ0Z01id1djK3lQN3Q5eE01WkEwNWFsaDNiSStSUGk4ZzNWSEZhR2phbmNYQnE0MUVpdlR0YTk5N3hzUUJmcTR' . $dynamicDatabaseName . 'E9PSIsInZhbHVlIjoiNjcrazQ1cEtNa2V'), time()+(3600 * 24 * 360));

        //Session::put('SM-DBN', $dynamicDatabaseNameEncrypted);
        //Session::save();

        // Make sure to use the new database connection
        DB::purge('smAppTemplate');
        DB::reconnect('smAppTemplate');

        return $next($request);
    }
}
