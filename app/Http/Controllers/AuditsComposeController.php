<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\AuditCompose;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuditsComposeController extends Controller
{
    // Specify the database connection to be used for this model
    protected $connection = 'smAppTemplate';

    public function index(Request $request)
    {
        return view('audits.compose');
    }


    public function show($id)
    {
        $compose = AuditCompose::findOrFail($id);

        if (!$compose) {
            abort(404);
        }

        return view('audits.compose', compact('compose') );
    }


    public function update(Request $request, $id = null)
    {
        // TODO
    }


    public function store(Request $request)
    {
        $jsonString = $request->input('item_steps');
        $steps = $jsonString ? json_decode($jsonString, true) : null;
        if (json_last_error() !== JSON_ERROR_NONE) {
            // Handle JSON decode error
            return response()->json(['success' => false, 'message' => 'Invalid JSON format']);
        }

        //return response()->json(['success' => false, 'message' => json_decode($jsonString, true)]);


        if(!$steps){
            return response()->json(['success' => false, 'message' => 'Error saving data']);
        }

        $user = Auth::user();
        $title = $request->input('title');

        $auditCompose = new AuditCompose;
        $auditCompose->user_id = $user->id;
        $auditCompose->title = $title;
        $auditCompose->jsondata = $steps ? $steps : '';
        $auditCompose->status = 'publish';
        $auditCompose->save();

        return response()->json(['success' => true, 'json' => json_decode($jsonString, true)]);
    }



}
