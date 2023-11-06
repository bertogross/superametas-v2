<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\SurveyCompose;
use App\Models\Survey;
use App\Models\SurveyMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SurveysController extends Controller
{
    // Specify the database connection to be used for this model
    protected $connection = 'smAppTemplate';

    public function index(Request $request)
    {
        $user = Auth::user();

        $status = $request->input('status');
        $delegated_to = $request->input('delegated_to');
        $audited_by = $request->input('audited_by');

        $created_at = $request->input('created_at');

        $query = Survey::query();

        // Search by status
        if ($status) {
            $query->where('status', $status);
        }

        $query = $query->where('created_by', $user->id);

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

        $usersByRole = getUsersByRole(User::ROLE_AUDIT);

        $delegated_to = request('delegated_to');
        $delegated_to = is_array($delegated_to) ? $delegated_to : array();

        $audited_by = request('audited_by');
        $audited_by = is_array($audited_by) ? $audited_by : array();

        return view('surveys.index', compact(
            'surveys',
            'users',
            'usersByRole',
            'surveyStatusCount',
            'getSurveyStatusTranslations',
            'delegated_to',
            'audited_by'
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
        session()->forget('success');

        $survey = $surveyComposeCustomId = $surveyComposeDefaultId = null;

        $users = getUsers();

        $usersByRole = getUsersByRole(User::ROLE_AUDIT);

        $getAuthorizedCompanies = getAuthorizedCompanies();

        $getActiveDepartments = getActiveDepartments();

        $getSurveyStatusTranslations = Survey::getSurveyStatusTranslations();

        $custom = SurveyCompose::getAllByType('custom');
        $getSurveyComposeCustom = $custom ?? null;

        $default = SurveyCompose::getAllByType('default');
        $getSurveyComposeDefault = $default ?? null;

        return view('surveys.create', compact(
            'survey',
            'users',
            'usersByRole',
            'getAuthorizedCompanies',
            'getSurveyStatusTranslations',
            'getSurveyComposeCustom',
            'getSurveyComposeDefault',
            'getActiveDepartments',
            'surveyComposeCustomId',
            'surveyComposeDefaultId'
        ) );
    }

    /**
     * Get the content for the modal because contains form
     *
     * @param int|null
     * @return \Illuminate\View\View
     */
    public function edit(Request $request)
    {
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

        $users = getUsers();

        $usersByRole = getUsersByRole(User::ROLE_AUDIT);

        $getAuthorizedCompanies = getAuthorizedCompanies();

        $getSurveyStatusTranslations = Survey::getSurveyStatusTranslations();

        $custom = SurveyCompose::getAllByType('custom');
        $getSurveyComposeCustom = $custom ?? null;

        $surveyComposeCustomId = SurveyMeta::getSurveyMeta($surveyId, 'survey_compose_custom_id');

        $default = SurveyCompose::getAllByType('default');
        $getSurveyComposeDefault = $default ?? null;

        $surveyComposeDefaultId = SurveyMeta::getSurveyMeta($surveyId, 'survey_compose_default_id');

        $getActiveDepartments = getActiveDepartments();

        $view = view('surveys.edit', compact(
            'survey',
            'users',
            'usersByRole',
            'getAuthorizedCompanies',
            'getSurveyStatusTranslations',
            'getSurveyComposeCustom',
            'getSurveyComposeDefault',
            'getActiveDepartments',
            'surveyComposeCustomId',
            'surveyComposeDefaultId'
            )
        );

        return $view;
    }


    public function createOrUpdate(Request $request, $id = null)
    {
        $validatedData = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,audited',
            'assigned_to' => 'nullable',
            'due_date' => 'nullable|date_format:d/m/Y',
            'delegated_to' => 'nullable',
            'audited_by' => 'nullable',
            'current_user_editor' => 'nullable',
            'description' => 'nullable|string|max:1000',
            'survey_compose_custom_id' => 'nullable',
            'survey_compose_default_id' => 'nullable',
            //'custom_fields' => 'nullable|array',
            //'custom_fields.*.type' => 'required_with:custom_fields|string|max:20',
            //'custom_fields.*.name' => 'required_with:custom_fields|string|max:30',
            //'custom_fields.*.label' => 'required_with:custom_fields|string|max:50',
        ]);

        if ($id) {
            // Update operation
            $survey = Survey::findOrFail($id);

            // Check if the current user is the creator
            if (auth()->id() != $survey->created_by) {
                return redirect()->back()->with('error', 'You are not authorized to edit this survey task.');
            }

            $validatedData['due_date'] = $validatedData['due_date'] ?? null ? Carbon::createFromFormat('d/m/Y', $validatedData['due_date'])->format('Y-m-d') : null;
            $validatedData['completed_at'] = $validatedData['completed_at'] ?? null ? Carbon::createFromFormat('d/m/Y', $validatedData['completed_at'])->format('Y-m-d') : null;
            $validatedData['audited_at'] = $validatedData['audited_at'] ?? null ? Carbon::createFromFormat('d/m/Y', $validatedData['audited_at'])->format('Y-m-d') : null;

            $survey->update($validatedData);

            // Update custom fields
            $composeCustomId = $validatedData['survey_compose_custom_id'];
            SurveyMeta::updateSurveyMeta($survey->id, 'survey_compose_custom_id', $composeCustomId);

            $composeDefaultId = $validatedData['survey_compose_default_id'];
            SurveyMeta::updateSurveyMeta($survey->id, 'survey_compose_default_id', $composeDefaultId);

            //return redirect()->route('surveysShowURL', $survey)->with('success', 'Survey updated successfully');
            return response()->json(['success' => true, 'message' => 'Survey updated successfully!']);
        } else {
            // Store operation
            $survey = $this->store($request);

            // Update custom fields
            $composeCustomId = $validatedData['survey_compose_custom_id'];
            SurveyMeta::updateSurveyMeta($survey->id, 'survey_compose_custom_id', $composeCustomId);

            $composeDefaultId = $validatedData['survey_compose_default_id'];
            SurveyMeta::updateSurveyMeta($survey->id, 'survey_compose_default_id', $composeDefaultId);

            return $survey;

            return response()->json(['success' => true, 'message' => 'Survey saved successfully!']);
        }
    }
}
