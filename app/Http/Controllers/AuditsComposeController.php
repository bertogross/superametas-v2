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

    // Listing
    public function index()
    {
        $composes = AuditCompose::all();

        return view('audits.compose.index', compact('composes') );
    }

    // Add
    public function add()
    {
        $compose = null;

        return view('audits.compose.create', compact('compose') );
    }

    // Edit
    public function edit($id)
    {
        $compose = AuditCompose::findOrFail($id);

        $decode = is_string($compose->jsondata) ? json_decode($compose->jsondata, true) : $compose->jsondata;

        $jsondata = $this->transformData($decode);

        $view = view('audits.compose.edit', compact('compose', 'jsondata'));

        session()->forget('success');

        return $view;
    }

    // Show
    public function show(Request $request, $id = null)
    {
        $compose = AuditCompose::findOrFail($id);

        if (!$compose) {
            abort(404);
        }

        $preview = $request->query('preview', false);

        $decode = is_string($compose->jsondata) ? json_decode($compose->jsondata, true) : $compose->jsondata;

        $jsondata = $this->transformData($decode);

        return view('audits.compose.show', compact('compose', 'jsondata', 'preview') );
    }


    private function transformData($data) {
        $transformedData = [];

        if($data){
            // First, sort the steps according to their new_position
            foreach ($data as $step) {
                $newPosition = $step['stepData']['new_position'] ?? 0;
                $transformedData[$newPosition] = $step;
            }
            ksort($transformedData); // Sort by key to maintain the order of steps

            // Now, sort the topicData for each step
            foreach ($transformedData as $stepPosition => &$step) {
                $sortedTopicData = [];

                if( isset($step['topicData']) ){
                    foreach ($step['topicData'] as $topic) {
                        $newTopicPosition = $topic['new_position'] ?? 0;
                        $sortedTopicData[$newTopicPosition] = $topic;
                    }
                    ksort($sortedTopicData); // Sort by key to maintain the order of topics

                    $step['topicData'] = array_values($sortedTopicData); // Re-index the array
                }
            }
            unset($step); // Break the reference to the last element
        }
        return $transformedData;
    }



    // Update
    public function update(Request $request, $id = null)
    {
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid ID']);
        }

        $auditCompose = AuditCompose::find($id);
        if (!$auditCompose) {
            return response()->json(['success' => false, 'message' => 'AuditCompose not found']);
        }

        $user = Auth::user();
        if ($auditCompose->user_id != $user->id) {
            return response()->json(['success' => false, 'message' => 'You are not authorized to update this AuditCompose']);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'item_steps' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $jsonString = $request->input('item_steps');
        $steps = $jsonString ? json_decode($jsonString, true) : null;
        if (json_last_error() !== JSON_ERROR_NONE) {
            // Handle JSON decode error
            return response()->json(['success' => false, 'message' => 'Invalid JSON format']);
        }

        if (!$steps) {
            return response()->json(['success' => false, 'message' => 'Error saving data']);
        }

        $title = $request->input('title');

        $auditCompose->title = $title;
        $auditCompose->jsondata = $steps ? $steps : '';
        //$auditCompose->status = 'active';
        $auditCompose->save();

        //return response()->json(['success' => true, 'json' => json_decode($jsonString, true)]);

        session(['success' => 'Dados atualizados']);

        return response()->json(['success' => true, 'id' => $auditCompose->id, 'json' => json_decode($jsonString, true)]);

    }

    // Store
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'item_steps' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

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
        //$auditCompose->status = 'active';
        $auditCompose->save();

        session(['success' => 'Dados inseridos']);

        //return response()->json(['success' => true, 'json' => json_decode($jsonString, true)]);
        return response()->json(['success' => true, 'id' => $auditCompose->id, 'json' => json_decode($jsonString, true)]);
    }

    // Status toggle
    public function toggleStatus(Request $request, $id, $status)
    {
        $user = Auth::user();
        $auditCompose = AuditCompose::find($id);
        if (!$auditCompose) {
            return response()->json(['success' => false, 'message' => 'Audit compose not found']);
        }

        if ($auditCompose->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'You are not authorized to change the status of this audit compose']);
        }

        if (!in_array($status, ['active', 'disabled'])) {
            return response()->json(['success' => false, 'message' => 'Invalid status']);
        }

        $auditCompose->status = $status;
        $auditCompose->save();

        // Store status message in session
        $translatedStatus = $status === 'active' ? 'ativo' : 'desabilitado';
        session(['success' => 'Status atualizado para: ' . $translatedStatus]);

        return response()->json(['success' => true, 'message' => 'Audit compose status updated successfully']);

    }

}
