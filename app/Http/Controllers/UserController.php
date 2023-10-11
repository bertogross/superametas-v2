<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('settings-users', compact('users'));
    }

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

    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:191',
            'email' => ['required', 'string', 'email', 'max:191'],
            'role' => 'required|intereger|max:10',
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg', 'max:1024'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg', 'max:1024'],
        ]);

        if ($request->file('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
            $avatarPath = public_path('/images/');
            $avatar->move($avatarPath, $avatarName);
            $user->avatar =  $avatarName;
        }
        if ($request->file('cover')) {
            $cover = $request->file('cover');
            $coverName = time() . '.' . $cover->getClientOriginalExtension();
            $coverPath = public_path('/images/');
            $cover->move($coverPath, $coverName);
            $user->cover =  $coverName;
        }

        // Create a new user
        User::create($request->all());

        // Redirect back with a success message
        return redirect()->back()->with('success', 'User added successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'email', 'max:191'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg', 'max:1024'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg', 'max:1024'],
        ]);

        $user = User::find($id);
        $user->name = strip_tags( $request->get('name') );
        $user->email = strip_tags( $request->get('email') );

        if ($request->file('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
            $avatarPath = public_path('/images/');
            $avatar->move($avatarPath, $avatarName);
            $user->avatar =  $avatarName;
        }
        if ($request->file('cover')) {
            $cover = $request->file('cover');
            $coverName = time() . '.' . $cover->getClientOriginalExtension();
            $coverPath = public_path('/images/');
            $cover->move($coverPath, $coverName);
            $user->cover =  $coverName;
        }

        $user->update();
        if ($user) {
            Session::flash('message', 'User Details Updated successfully!');
            Session::flash('alert-class', 'alert-success');
            // return response()->json([
            //     'isSuccess' => true,
            //     'Message' => "User Details Updated successfully!"
            // ], 200); // Status code here
            return redirect()->back();
        } else {
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
            // return response()->json([
            //     'isSuccess' => true,
            //     'Message' => "Something went wrong!"
            // ], 200); // Status code here
            return redirect()->back();

        }
    }


}
