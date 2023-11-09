<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\SurveyCompose;
use App\Models\SurveyTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SurveysComposeController extends Controller
{
    // Specify the database connection to be used for this model
    protected $connection = 'smAppTemplate';

    // Listing
    public function index()
    {
        $custom = SurveyCompose::getAllByType('custom');
        $custom = $custom ?? null;

        $default = SurveyCompose::getAllByType('default');
        $default = $default ?? null;

        return view('surveys.compose.listing', compact('custom', 'default') );
    }

    public function create($type = 'custom')
    {
        session()->forget('success');

        $data = $topicsData = null;

        $getActiveDepartments = getActiveDepartments();

        return view('surveys.compose.create', compact(
            'type',
            'data',
            'topicsData',
            'getActiveDepartments'
            ) );
    }

    public function edit($id)
    {
        // Cache::flush();

        $data = SurveyCompose::findOrFail($id);

        $type = $data->type;

        $decode = is_string($data->jsondata) ? json_decode($data->jsondata, true) : $data->jsondata;

        $topicsData = SurveyCompose::reorderingData($decode);

        $getActiveDepartments = getActiveDepartments();

        session()->forget('success');

        return view('surveys.compose.edit', compact(
            'type',
            'data',
            'topicsData',
            'getActiveDepartments'
        ));
    }

    public function show(Request $request, $id = null)
    {
        $data = SurveyCompose::findOrFail($id);

        if (!$data) {
            abort(404);
        }

        $users = getUsers();

        $preview = $request->query('preview', false);

        $edition = $request->query('edition', false);

        $decode = is_string($data->jsondata) ? json_decode($data->jsondata, true) : $data->jsondata;

        $topicsData = SurveyCompose::reorderingData($decode);

        return view('surveys.compose.show', compact(
            'data',
            'topicsData',
            'preview',
            'edition',
            'users'
        ) );
    }


    public function storeOrUpdate(Request $request, $id = null)
    {
        // Cache::flush();

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'item_steps' => 'required|string',
            'type' => 'required|in:custom,default',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $user = Auth::user();
        $title = e($request->input('title'));
        $type = e($request->input('type'));

        $jsonString = $request->input('item_steps');
        $steps = $jsonString ? json_decode($jsonString, true) : null;
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['success' => false, 'message' => 'Invalid JSON format']);
        }

        if (!$steps) {
            return response()->json(['success' => false, 'message' => 'Error saving data']);
        }

        $data = [
            'user_id' => $user->id,
            'title' => $title,
            'jsondata' => $steps,
            'type' => $type
            // 'status' => 'active', // Uncomment if you want to set status to active on every save/update
        ];

        $surveyCompose = SurveyCompose::updateOrCreate(['id' => $id], $data);

        $message = $id ? 'Dados atualizados' : 'Dados inseridos';
        session(['success' => $message]);

        return response()->json([
            'success' => true,
            'id' => $surveyCompose->id,
            'type' => $type,
            'json' => $steps
        ]);
    }

    // Status toggle
    public function toggleStatus(Request $request, $id, $status)
    {
        $user = Auth::user();
        $surveyCompose = SurveyCompose::find($id);
        if (!$surveyCompose) {
            return response()->json(['success' => false, 'message' => 'Survey compose not found']);
        }

        if ($surveyCompose->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'You are not authorized to change the status of this survey compose']);
        }

        if (!in_array($status, ['active', 'disabled'])) {
            return response()->json(['success' => false, 'message' => 'Invalid status']);
        }

        $surveyCompose->status = $status;
        $surveyCompose->save();

        // Store status message in session
        $translatedStatus = $status === 'active' ? 'ativo' : 'desabilitado';
        session(['success' => 'Status atualizado para: ' . $translatedStatus]);

        return response()->json(['success' => true, 'message' => 'Survey compose status updated successfully']);

    }

    public function getTermNameById($termId) {
        return SurveyTerm::getTermNameById($termId);
    }

}
