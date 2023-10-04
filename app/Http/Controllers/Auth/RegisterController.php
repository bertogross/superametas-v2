<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:200'],
            'email' => ['required', 'string', 'email', 'max:200', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            //'avatar' => ['required', 'image' ,'mimes:jpg,jpeg,png','max:1024'],
            //'subdomain' => ['required', 'string', 'max:100'],
            'subdomain' => [
                'required',
                'string',
                'max:100',
                /*
                'regex:/^\S+$/u', // To check for no white spaces
                Rule::unique('users', 'subdomain')->where(function ($query) use ($data) {
                    return $query->where('subdomain', strtolower($data['subdomain']));
                }),
                */ // RELATED TO TENANCY FOR LARAVEL
            ]
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // return request()->file('avatar');
        if (request()->has('avatar')) {
            $avatar = request()->file('avatar');
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
            $avatarPath = public_path('/images/');
            $avatar->move($avatarPath, $avatarName);
        }

        //return User::create([
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            //'avatar' =>  $avatarName,
        ]);

        /*
        // RELATED TO TENANCY FOR LARAVEL
        $subdomain = strtolower(trim($data['subdomain']));
        $newUser = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            //'avatar' =>  $avatarName,
            'subdomain' =>  $subdomain,
        ]);

        $newUserId = $newUser->id;

        $tenant = \App\Models\Tenant::create(['id' => 'App'.$newUserId.'']);
        $tenant->domains()->create(['domain' => $subdomain.'.'.env('APP_DOMAIN')]);

        $tenantDatabaseName = 'tenantApp'.$newUserId.'';

        // Path to default_schema.sql file
        $sqlFilePath = base_path('database/default_schema/tenancy.sql');

        // Read the SQL file
        $sql = file_get_contents($sqlFilePath);

        // Switch to the tenant database
        DB::statement("USE $tenantDatabaseName");

        // Execute the SQL statements from the file
        DB::unprepared($sql);

        return $newUser;
        // RELATED TO TENANCY FOR LARAVEL
        */
    }
}
