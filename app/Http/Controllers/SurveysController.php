<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
//use App\Models\SurveyCompose;
use App\Models\Survey;
use App\Models\SurveyMeta;
use App\Models\SurveyTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SurveysController extends Controller
{
    // Specify the database connection to be used for this model
    protected $connection = 'smAppTemplate';

    public function index(Request $request)
    {
        $status = $request->input('status');
        $delegated_to = $request->input('delegated_to');
        $audited_by = $request->input('audited_by');

        $created_at = $request->input('created_at');

        $query = Survey::query();

        // Search by status
        if ($status) {
            $query->where('status', $status);
        }

        //$user = auth()->id();
        //$query = $query->where('user_id', $user->id);

        // Search by delegated_to and or audited_by
        /*
        if ($delegated_to) {
            $query->whereIn('delegated_to', $delegated_to);
        } elseif ($audited_by) {
            $query->whereIn('audited_by', $audited_by);
        } elseif ($audited_by && $delegated_to) {
            $query->whereIn('audited_by', $audited_by)->orWhereIn('delegated_to', $delegated_to);
        }
        */
        if ($delegated_to && $audited_by) {
            $query->where(function ($query) use ($delegated_to, $audited_by) {
                $query->whereIn('delegated_to', $delegated_to)
                      ->orWhereIn('audited_by', $audited_by);
            });
        } elseif ($delegated_to) {
            $query->whereIn('delegated_to', $delegated_to);
        } elseif ($audited_by) {
            $query->whereIn('audited_by', $audited_by);
        }

        if ($created_at) {
            $dates = explode(' até ', $created_at);
            if (is_array($dates) && count($dates) === 2) {
                $start_date = Carbon::createFromFormat('d/m/Y', $dates[0])->format('Y-m-d');
                $end_date = Carbon::createFromFormat('d/m/Y', $dates[1])->format('Y-m-d') . ' 23:59:59';
                $query->whereBetween('created_at', [$start_date, $end_date]);
            } else {
                $start_date = Carbon::createFromFormat('d/m/Y', $created_at)->format('Y-m-d');
                $query->whereDate('created_at', '=', $start_date);
            }
        }

        $surveys = $query->orderBy('updated_at')->paginate(10);

        $surveyStatusCount = Survey::countByStatus(); // Call the function on the Survey model

        $getSurveyStatusTranslations = Survey::getSurveyStatusTranslations();

        $users = getUsers();

        //$usersByRole = getUsersByRole(User::ROLE_CONTROLLERSHIP);

        $delegated_to = request('delegated_to');
        $delegated_to = is_array($delegated_to) ? $delegated_to : array();

        $audited_by = request('audited_by');
        $audited_by = is_array($audited_by) ? $audited_by : array();

        $dateRange = Survey::getSurveysDateRange();
        $firstDate = $dateRange['first_date'];
        $lastDate = $dateRange['last_date'];

        return view('surveys.listing', compact(
            'surveys',
            'users',
            //'usersByRole',
            'surveyStatusCount',
            'getSurveyStatusTranslations',
            'delegated_to',
            'audited_by',
            'firstDate',
            'lastDate'
        ));
    }

    public function show($id = null)
    {
        /*
        $survey = Survey::findOrFail($id);

        if (!$survey) {
            abort(404);
        }

        return view('surveys.show', compact('survey') );
        */
        $survey = Survey::findOrFail($id);

        return view('surveys.show', compact('survey'));
    }

    // Add
    public function create()
    {
        // Cache::flush();

        session()->forget('success');

        $survey = null;

        //$surveyComposeCustomId = $surveyComposeDefaultId = null;

        $users = getUsers();

        //$usersByRole = getUsersByRole(User::ROLE_CONTROLLERSHIP);

        $getAuthorizedCompanies = getAuthorizedCompanies();

        $getActiveDepartments = getActiveDepartments();

        $getSurveyStatusTranslations = Survey::getSurveyStatusTranslations();

        //$custom = Survey::getAllByType('custom');
        //$getSurveyComposeCustom = $custom ?? null;

        //$default = Survey::getAllByType('default');
        //$getSurveyComposeDefault = $default ?? null;

        $survey = $Custom = null;

        $preListing = SurveyTerm::preListing();
        $Default = [];
        foreach($getActiveDepartments as $index => $department){
            $Default[$index] = [
                'stepData' => [
                    'step_name' => $department->department_alias,
                    'step_id' => $department->id,
                    'original_position' => $index,
                    'new_position' => $index,
                ],
                'topics' => $preListing
            ];
        }

        return view('surveys.create', compact(
                'survey',
                'users',
                //'data',
                'Default',
                'Custom',
                //'usersByRole',
                'getAuthorizedCompanies',
                'getSurveyStatusTranslations',
                'getActiveDepartments',
                //'getSurveyComposeCustom',
                //'getSurveyComposeDefault',
                //'surveyComposeCustomId',
                //'surveyComposeDefaultId'
            )
        );
    }

    public function edit(Request $request)
    {
        // Cache::flush();

        session()->forget('success');

        $survey = [];

        $surveyId = request('id');

        //$survey = Survey::findOrFail($surveyId);

        if($surveyId){
            $survey = DB::connection($this->connection)
                ->table('surveys')
                ->where('id', $surveyId)
                ->get()
                ->toArray();

            $survey = $survey[0] ?? '';
        }

        $decode = is_string($survey->jsondata) ? json_decode($survey->jsondata, true) : $survey->jsondata;

        $data = Survey::reorderingData($decode);

        $custom = Survey::getByType($data, 'custom');
        $Custom = $custom ?? null;

        //$surveyComposeCustomId = SurveyMeta::getSurveyMeta($surveyId, 'survey_compose_custom_id');

        $default = Survey::getByType($data, 'default');
        $Default = $default ?? null;

        //$surveyComposeDefaultId = SurveyMeta::getSurveyMeta($surveyId, 'survey_compose_default_id');

        $users = getUsers();

        //$usersByRole = getUsersByRole(User::ROLE_CONTROLLERSHIP);

        $getAuthorizedCompanies = getAuthorizedCompanies();

        $getActiveDepartments = getActiveDepartments();

        $getSurveyStatusTranslations = Survey::getSurveyStatusTranslations();

        $view = view('surveys.edit', compact(
            'users',
            'survey',
            'Custom',
            'Default',
            //'usersByRole',
            'getAuthorizedCompanies',
            'getActiveDepartments',
            'getSurveyStatusTranslations',
            //'surveyComposeCustomId',
            //'surveyComposeDefaultId'
            )
        );

        return $view;
    }

    public function storeOrUpdate(Request $request, $id = null)
    {
        // Cache::flush();

        $validatedData = $request->validate([
            'title' => 'required|string|max:191',
            'recurring' => 'required|in:once,daily,weekly,biweekly,monthly,annual',
            'start_date' => 'nullable|date_format:d/m/Y',
            'description' => 'nullable|string|max:1000',

            'delegated_to' => 'required',
            'audited_by' => 'required',
            'jsondata' => 'required',
            //'current_user_editor' => 'nullable',
            //'assigned_to' => 'nullable',
            //'status' => 'required|in:new,stoped,trash,pending,in_progress,completed,audited',
            //'survey_compose_custom_id' => 'nullable',
            //'survey_compose_default_id' => 'nullable',
            //'custom_fields' => 'nullable|array',
            //'custom_fields.*.type' => 'required_with:custom_fields|string|max:20',
            //'custom_fields.*.name' => 'required_with:custom_fields|string|max:30',
            //'custom_fields.*.label' => 'required_with:custom_fields|string|max:50',
        ]);

        // Convert array inputs to JSON strings for storage
        $validatedData = array_map(function ($value) {
            return is_array($value) ? json_encode($value) : $value;
        }, $validatedData);

        $jsondata = $validatedData['jsondata'];

        /*
        $jsonString = $request->input('jsondata');
        $jsondata = $jsonString ? json_decode($jsonString, true) : null;
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['success' => false, 'message' => 'Invalid JSON format']);
        }
        $validatedData['jsondata'] = $jsondata ?? null;


        $jsonString = $request->input('delegated_to');
        $jsondata = $jsonString ? json_decode($jsonString, true) : null;
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['success' => false, 'message' => 'Invalid JSON format']);
        }
        $validatedData['delegated_to'] = $jsondata ?? null;


        $jsonString = $request->input('audited_by');
        $jsondata = $jsonString ? json_decode($jsonString, true) : null;
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['success' => false, 'message' => 'Invalid JSON format']);
        }
        $validatedData['audited_by'] = $jsondata ?? null;
        */

        $userId = auth()->id();
        $validatedData['user_id'] = $userId;

        $validatedData['start_date'] = $validatedData['start_date'] ? Carbon::createFromFormat('d/m/Y', $validatedData['start_date'])->format('Y-m-d') : null;
        //$validatedData['completed_at'] = $validatedData['completed_at'] ?? null ? Carbon::createFromFormat('d/m/Y', $validatedData['completed_at'])->format('Y-m-d') : null;
        //$validatedData['audited_at'] = $validatedData['audited_at'] ?? null ? Carbon::createFromFormat('d/m/Y', $validatedData['audited_at'])->format('Y-m-d') : null;

        if ($id) {
            // Update operation
            $survey = Survey::findOrFail($id);

            // Check if the current user is the creator
            if ($userId != $survey->user_id) {
                return response()->json(['success' => false, 'message' => 'You are not authorized to edit this survey.']);
            }

            $survey->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Survey updated successfully!',
                'id' => $survey->id,
                'json' => $jsondata
            ]);
        } else {
            // Store operation
            $survey = new Survey;
            $survey->fill($validatedData);
            $survey->save();

            return response()->json([
                'success' => true,
                'message' => 'Survey saved successfully!',
                'id' => $survey->id,
                'json' => $jsondata
            ]);
        }
    }
}
