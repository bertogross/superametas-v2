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
            return redirect('/settings-account'); // Redirect admins to settings-account
        }

        // Load the view
        return view('profile-settings');
    }

}
