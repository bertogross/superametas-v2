<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class ProfileController extends Controller
{
    public function settings()
    {
        // Ensure the user is authenticated
        if (!Auth::check()) {
            return redirect('/login'); // or wherever you want to redirect unauthenticated users
        }

        // Check if the authenticated user has ROLE_ADMIN
        if (Auth::user()->hasRole(User::ROLE_ADMIN)) {
            return redirect('/settings/account'); // Redirect admins to settings/account
        }

        // Load the view
        return view('profile/settings');
    }

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

}
