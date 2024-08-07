<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Survey;
use Illuminate\Http\Request;


class TeamController extends Controller
{
    public function index()
    {
        $users = User::all();

        // Usefull if crontab or Kernel schedule is losted
        Survey::populateSurveys();

        return view('team.index', compact('users'));
    }
}
