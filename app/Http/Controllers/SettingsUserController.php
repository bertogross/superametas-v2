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
use App\Http\Controllers\OnboardController;
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

        return view('settings.users', compact('users'));
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
            /*if(auth()->id() !== $user->id) {
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
            'email.max' => 'The email may not be greater than 100 characters.',
            'capabilities.*.in' => 'Regra de usuário conflitante',
        ];

        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:100',
            'capabilities.*' => 'in:'.implode(',', array_keys(User::CAPABILITY_TRANSLATIONS)),
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

        $request['capabilities'] = $request->capabilities ? json_encode($request->capabilities) : null;

        // Check if email exists in another smApp database and copu
        $password = OnboardController::getPasswordFromOtherDatabases($request->email);

        // Generate a random password if not found in other databases
        if (!$password) {
            $password = Str::random(10);

            // Hash the password
            $hashedPassword = bcrypt($password);

            $message = "Usuário adicionado!<br> Login: ".$request->email."<br>Senha: ".$password."";
        }else{
            $hashedPassword = $password;

            $message = "Usuário adicionado!<br> Login: ".$request->email."<br>Senha: A mesma que já possui originada de outra(s) conta(s).";
        }

        // Create a new user
        $user = User::create(array_merge($request->all(), ['password' => $hashedPassword]));

        // After creating the user
        $companies = is_array($request->get('companies')) ? json_encode(array_map('intval', $request->get('companies'))) : $request->get('companies');
        SettingsUserController::updateUserMeta($user->id, 'companies', $companies);

        if ($user->save()) {
            // Call the new method to update or create a record in app_subusers
            OnboardController::updateOrCreateSubUser($user->email);

            return response()->json([
                'success' => true,
                'message' => $message,
                'getOtherDatabases' => json_encode(OnboardController::getOtherDatabases($user->email))
            ], 200);

        } else {
            return response()->json(['success' => false, 'message' => "Erro ao salvar dados de usuário"], 200);
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
        // Custom error messages
        $messages = [
            'name.required' => 'The company name is required.',
            'name.max' => 'The company name may not be greater than 191 characters.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'The email may not be greater than 100 characters.',
            'new_password.min' => 'The password must be at least 8 characters.',
            'new_password.max' => 'The password may not be greater than 20 characters.',
            'capabilities.*.in' => 'Regra de usuário conflitante',
        ];

        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:100',
            'new_password' => 'nullable|string|min:8|max:20',
            'capabilities.*' => 'in:'.implode(',', array_keys(User::CAPABILITY_TRANSLATIONS)),
        ], $messages);

        $user = User::find($id);

        if( !$user ){
            return response()->json(['success' => false, 'message' => "ID de Usuário não corresponde a esta base de dados"], 200);
        }

        // Check and set role based on conditions
        if ($user->id != 1 && ( empty($request->role) || ( intval($request->role) > 5 || intval($request->role) < 1 ) )) {
            return response()->json(['success' => false, 'message' => "Selecione o Nível"], 200);
        }

        if ($user->id == 1 || $user->role == 1) {
            $user->role = 1;
            $user->status = 1;
        }else{
            $user->role = $request->get('role') ? intval($request->get('role')) : 5;
            $user->status = $request->get('status') ? intval( $request->get('status') ) : 0;
        }
        $user->name = strip_tags( $request->get('name') );
        $user->email = strip_tags( $request->get('email') );

        $user->capabilities = $request->get('capabilities', []);

        // After updating the user
        $companies = is_array($request->get('companies')) ? json_encode(array_map('intval', $request->get('companies'))) : $request->get('companies');
        SettingsUserController::updateUserMeta($user->id, 'companies', $companies);

        // Check if the password is being updated
        if ($request->has('new_password') && !empty($request->new_password)) {

            // Hash the new password
            $hashedPassword = bcrypt($request->new_password);

            // Update the password in the current database
            $user->password = $hashedPassword;

            // Update the password in other smApp databases
            SettingsUserController::updatePasswordInOtherDatabases($user->email, $hashedPassword);
        }

        if ($user->update()) {
            return response()->json(['success' => true, 'message' => "Dados de usuário foram atualizados"], 200);
        } else {
            return response()->json(['success' => false, 'message' => "Erro ao salvar dados de usuário"], 200);
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

    public static function updatePasswordInOtherDatabases($email, $hashedPassword)
    {
        // Get the list of other smApp databases
        $otherDatabases = OnboardController::getOtherDatabases($email);

        // Update the password in each database
        foreach ($otherDatabases as $data) {
            // Skip the current database
            if ($data['database'] == config('database.connections.smAppTemplate.database')) {
                continue;
            }

            $databaseExists = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$data['database']]);
            if (!$databaseExists) {
                return null;
            }

            // Set the database connection configuration for the other database
            config([
                'database.connections.otherDatabase' => [
                    'driver' => 'mysql',
                    'host' => env('DB_HOST'),
                    'port' => env('DB_PORT'),
                    'database' => $data['database'],
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

        return view('settings.users-form', compact('user'));

    }
}
