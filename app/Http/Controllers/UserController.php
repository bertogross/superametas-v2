<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

/**
 * UserController
 *
 * Controller responsible for handling user-related actions.
 */
class UserController extends Controller
{
    /**
     * Display a listing of all users.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $users = User::all();
        return view('settings-users', compact('users'));
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

        return view('profile', compact('user'));
    }

    /**
     * Store a newly created user in the database.
     *
     * @param \Illuminate\Http\Request $request The incoming request.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:191',
            'email' => ['required', 'string', 'email', 'max:191'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg', 'max:1024'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg', 'max:1024'],
        ]);

        // Check if email exist
        $exists = DB::connection('smAppTemplate')
            ->table('users')
            ->where('email', $request->email)
            ->first();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'O endereço de e-mail <span class="text-theme">'.$exists->email.'</span> está sendo utilizado pelo usuário <span class="text-theme">'.$exists->name.'</span>'], 200);
        }

        // Check and set role based on conditions
        if (empty($request->role)) {
            //$request->merge(['role' => 4]);
            return response()->json(['success' => false, 'message' => "Selecione o Nível"], 200);
        }

        // Generate a random password
        $password = Str::random(10); // generates a random 10-character string

        // Hash the password
        $hashedPassword = bcrypt($password);

        // Create a new user
        $user = User::create(array_merge($request->all(), ['password' => $hashedPassword]));

        if ($request->file('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
            $avatarPath = public_path('/images/');
            $avatar->move($avatarPath, $avatarName);
            $user->avatar = $avatarName;
        }
        if ($request->file('cover')) {
            $cover = $request->file('cover');
            $coverName = time() . '.' . $cover->getClientOriginalExtension();
            $coverPath = public_path('/images/');
            $cover->move($coverPath, $coverName);
            $user->cover = $coverName;
        }

        // After creating the user
        $companies = $request->get('companies'); // Assuming 'companies' is the name of the checkboxes
        $this->updateUserMeta($user->id, 'companies', $companies);

        if ($user->save()) {
            return response()->json([
                'success' => true,
                'message' => "User added successfully!<br> Login: ".$user->email."<br>Password: ".$password.""
            ], 200);

        } else {
            return response()->json(['success' => false, 'message' => "Error saving user"], 200);
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

        $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'email', 'max:191'],
            //'role' => 'required|integer|max:1|not_in:1', // Add the not_in:1 rule here
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg', 'max:1024'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg', 'max:1024'],
        ]);

        // Check and set role based on conditions
        if (empty($request->role)) {
            //$request->merge(['role' => 4]);
            return response()->json(['success' => false, 'message' => "Selecione o Nível"], 200);
        }

        $user = User::find($id);

        if ($user->id == 1) {
            $user->role = 1;
        }else{
            $user->role = intval($request->get('role'));
        }
        $user->name = strip_tags( $request->get('name') );
        $user->email = strip_tags( $request->get('email') );
        $user->status = intval( $request->get('status') );

        if ($request->file('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
            $avatarPath = public_path('/images/');
            $avatar->move($avatarPath, $avatarName);
            $user->avatar = $avatarName;
        }
        if ($request->file('cover')) {
            $cover = $request->file('cover');
            $coverName = time() . '.' . $cover->getClientOriginalExtension();
            $coverPath = public_path('/images/');
            $cover->move($coverPath, $coverName);
            $user->cover = $coverName;
        }

        // After updating the user
        $companies = $request->get('companies'); // Assuming 'companies' is the name of the checkboxes
        $this->updateUserMeta($user->id, 'companies', $companies);

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
    protected function updateUserMeta($userId, $key, $value) {
        return UserMeta::updateOrCreate(
            ['user_id' => $userId, 'meta_key' => $key],
            ['meta_value' => json_encode($value)]
        );
    }

    /**
     * Get the content for the user modal.
     *
     * @param int|null $id The user's ID.
     * @return \Illuminate\View\View
     */
    public function getUserModalContent($id = null) {
        $companies = DB::connection('smAppTemplate')
            ->table('wlsm_companies')
            ->orderBy('company_id', 'asc')
            ->where('status', 1)
            ->get();


        $user = null;
        $selectedCompanies = [];

        if ($id) {
            $user = User::find($id);

            $userMeta = UserMeta::where('user_id', $id)->where('meta_key', 'companies')->first();
            if ($userMeta) {
                $selectedCompanies = json_decode($userMeta->meta_value, true);
            }
        }

        return view('components.settings-users-modal-form', compact('companies', 'user', 'selectedCompanies'));

    }
}
