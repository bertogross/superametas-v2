<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;


class TeamController extends Controller
{
    public function index()
    {
        $users = User::all();

        return view('team.index', compact('users'));
    }
}
