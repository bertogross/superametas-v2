<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsApiKeysController extends Controller
{
    public function index()
    {
        return view('settings.api-keys');
    }
}
