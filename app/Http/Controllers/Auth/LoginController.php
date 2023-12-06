<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use App\Providers\RouteServiceProvider;
use App\Http\Controllers\OnboardController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
//use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Check if the user exists in the another databases
        $getOtherDatabases = OnboardController::getOtherDatabases($credentials['email']);

        if(!$getOtherDatabases){
            return redirect()->back()->withErrors(['email' => 'Falha de autenticação']);
        }

        // Get from the first account
        $databaseName = $request->database ? $request->database : $getOtherDatabases[0];

        // Set the dynamic database connection
        Config::set('database.connections.smAppTemplate.database', $databaseName);
        DB::purge('smAppTemplate');
        DB::reconnect('smAppTemplate');

        // Debugging: Log the current database connection
         \Log::info('Current Database Connection: ' . config('database.connections.smAppTemplate.database'));
        //dd(session()->all());


        // Authenticate using the dynamic database connection
        if (Auth::guard('web')->attempt($credentials, $request->filled('remember'))) {
            // Check if the user's status is 0
            if (Auth::user()->status == 0) {
                // Log the user out
                Auth::logout();

                // Redirect back with an error message
                return redirect()->back()->withErrors(['email' => 'Your account is inactive. Please contact support.']);
            }

            // Authentication passed
            return redirect()->intended('/');
        }

        // Handle failed authentication
        return redirect()->back()->withErrors(['email' => 'Falha de autenticação']);
    }


    public function logout(Request $request)
    {
        // Logout the user
        Auth::logout();

        //Session::forget('SM-DBN');

        // Forget cookies
        $cookie1 = Cookie::forget('SM-DBN');
        $cookie2 = Cookie::forget('superametas_session');
        // Add as many cookies as you want to forget

        // Redirect to the homepage or login page with the forgotten cookies
        return redirect('/')->withCookies([$cookie1, $cookie2]);
    }

    public function checkDatabases(Request $request)
    {

        $email = e($request->input('email'));

        if (empty($email)) {
            return response()->json(['error' => 'Email is required']);
        }

        $databases = OnboardController::getOtherDatabases($email);

        return response()->json(['databases' => $databases]);
    }

}
