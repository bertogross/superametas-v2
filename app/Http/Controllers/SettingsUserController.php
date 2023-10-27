<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserMeta;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

/**
 * SettingsUserController
 *
 * Controller responsible for handling user-related actions.
 */
class SettingsUserController extends Controller
{
    protected $connection = 'smAppTemplate';

    // Fill created_at
    public $timestamps = true;

    /**
     * Display a listing of all users.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $users = User::all();
        return view('settings/users', compact('users'));
    }

    /**
     * Display the specified user's profile.
     *
     * @param int|null $id The user's ID.
     * @return \Illuminate\View\View
     */
    public function show($id = null)
    {
        if (!$id && auth()->check()) {
            $user = auth()->user();
        } else {
            $user = User::findOrFail($id);

            // Check if the authenticated user is the same as the user being viewed
            /*if(auth()->user()->id !== $user->id) {
                abort(403, 'Unauthorized action.');
            }*/
        }

        return view('profile.index', compact('user'));
    }

    /**
     * Store a newly created user in the database.
     *
     * @param \Illuminate\Http\Request $request The incoming request.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Custom error messages
        $messages = [
            'name.required' => 'The company name is required.',
            'name.max' => 'The company name may not be greater than 191 characters.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'The email may not be greater than 100 characters.',
        ];

        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:100',
        ], $messages);

        // Check if email exist
        $exists = DB::connection('smAppTemplate')
            ->table('users')
            ->where('email', $request->email)
            ->first();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'O endereço de e-mail <span class="text-theme">'.$exists->email.'</span> está sendo utilizado pelo usuário <span class="text-theme">'.$exists->name.'</span>'], 200);
        }

        // Check and set role based on conditions
        if (empty($request->role) || ( intval($request->role) > 5 || intval($request->role) < 1 ) ) {
            return response()->json(['success' => false, 'message' => "Selecione o Nível"], 200);
        }

        // Check if email exists in another smApp database and copu
        $password = $this->getPasswordFromOtherDatabases($request->email);

        // Generate a random password if not found in other databases
        if (!$password) {
            $password = Str::random(10);

            // Hash the password
            $hashedPassword = bcrypt($password);

            $message = "Usuario adicionado!<br> Login: ".$user->email."<br>Senha: ".$password."";
        }else{
            $hashedPassword = $password;

            $message = "Usuario adicionado!<br> Login: ".$user->email."<br>Senha: A mesma que já possui originada de outra(s) conta(s).";
        }

        // Create a new user
        $user = User::create(array_merge($request->all(), ['password' => $hashedPassword]));

        // After creating the user
        $companies = is_array($request->get('companies')) ? json_encode(array_map('intval', $request->get('companies'))) : $request->get('companies');
        $this->updateUserMeta($user->id, 'companies', $companies);

        if ($user->save()) {
            // Call the new method to update or create a record in app_subusers
            $this->updateOrCreateSubUser($user->email);

            return response()->json([
                'success' => true,
                'message' => $message,
                'getOtherDatabases' => json_encode(getOtherDatabases($user->email))
            ], 200);

        } else {
            return response()->json(['success' => false, 'message' => "Error saving user"], 200);
        }
    }

    /**
     * Get the password of a user from other smApp databases.
     *
     * @param string $email The user's email.
     * @return string|null The user's password or null if not found.
     */
    protected function getPasswordFromOtherDatabases($email)
    {
        // Get the list of other smApp databases from smOnboard
        $otherDatabases = getOtherDatabases($email);

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




    /**
     * Update/Create subusers in smOnboard
     */
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

    /**
     * Update the specified user in the database.
     *
     * @param \Illuminate\Http\Request $request The incoming request.
     * @param int $id The user's ID.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        /*$request->validate([
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'email', 'max:191'],
        ]);*/
        // Custom error messages
        $messages = [
            'name.required' => 'The company name is required.',
            'name.max' => 'The company name may not be greater than 191 characters.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'The email may not be greater than 100 characters.',
            'new_password.min' => 'The password must be at least 8 characters.',
            'new_password.max' => 'The password may not be greater than 20 characters.',
        ];

        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:100',
            'new_password' => 'nullable|string|min:8|max:20',
        ], $messages);

        $user = User::find($id);

        // Check and set role based on conditions
        if ($user->id != 1 && ( empty($request->role) || ( intval($request->role) > 5 || intval($request->role) < 1 ) )) {
            return response()->json(['success' => false, 'message' => "Selecione o Nível"], 200);
        }

        if ($user->id == 1) {
            $user->role = 1;
            $user->status = 1;
        }else{
            $user->role = $request->get('role') ? intval($request->get('role')) : 5;
            $user->status = $request->get('status') ? intval( $request->get('status') ) : 0;
        }
        $user->name = strip_tags( $request->get('name') );
        $user->email = strip_tags( $request->get('email') );


        // After updating the user
        $companies = is_array($request->get('companies')) ? json_encode(array_map('intval', $request->get('companies'))) : $request->get('companies');
        $this->updateUserMeta($user->id, 'companies', $companies);

        // Check if the password is being updated
        if ($request->has('new_password') && !empty($request->new_password)) {

            // Hash the new password
            $hashedPassword = bcrypt($request->new_password);

            // Update the password in the current database
            $user->password = $hashedPassword;

            // Update the password in other smApp databases
            $this->updatePasswordInOtherDatabases($user->email, $hashedPassword);
        }


        if ($user->update()) {
            return response()->json(['success' => true, 'message' => "User Details Updated"], 200);
        } else {
            return response()->json(['success' => false, 'message' => "Error saving user"], 200);
        }
    }


    /**
     * Update or create a user's meta data.
     *
     * @param int $userId The user's ID.
     * @param string $key The meta key.
     * @param mixed $value The meta value.
     * @return \App\Models\UserMeta
     */
    public function updateUserMeta($userId, $metaKey, $metaValue)
    {
        // Update or create user meta
        UserMeta::updateOrCreate(
            ['user_id' => $userId, 'meta_key' => $metaKey],
            ['meta_value' => $metaValue]
        );
    }


    protected function updatePasswordInOtherDatabases($email, $hashedPassword)
    {
        // Get the list of other smApp databases
        $otherDatabases = getOtherDatabases($email);

        // Update the password in each database
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

            // Update the password in the other database
            DB::connection('otherDatabase')
                ->table('users')
                ->where('email', $email)
                ->update(['password' => $hashedPassword]);

            // Disconnect from the other database
            DB::disconnect('otherDatabase');
        }
    }



    /**
     * Get the content for the user modal.
     *
     * @param int|null $id The user's ID.
     * @return \Illuminate\View\View
     */
    public function getUserModalContent($id = null) {
        $user = null;

        if ($id) {
            $user = User::find($id);
        }

        return view('settings/users-modal-form', compact('user'));

    }
}
