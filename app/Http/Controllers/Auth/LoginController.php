<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Cookie;
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

        // Authenticate using the dynamic database connection
        if (Auth::guard('web')->attempt($credentials)) {
            // Authentication passed
            return redirect()->intended('dashboard');
        }

        // Handle failed authentication
        return redirect()->back()->withErrors(['email' => 'Authentication failed']);
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


}
