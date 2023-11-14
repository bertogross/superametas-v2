<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
//use App\Models\SurveyCompose;
use App\Models\Survey;
use App\Models\SurveyTemplates;
//use App\Models\SurveyMeta;
use App\Models\SurveyTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SurveysController extends Controller
{

    public function index(Request $request)
    {
        $created_at = $request->input('created_at');
        $status = $request->input('status');
        //$delegated_to = $request->input('delegated_to');
        //$audited_by = $request->input('audited_by');

        /**
         * START TEMPLATES QUERY
         */
        $queryTemplates = SurveyTemplates::query();

        if ($created_at) {
            $dates = explode(' até ', $created_at);
            if (is_array($dates) && count($dates) === 2) {
                $start_date = Carbon::createFromFormat('d/m/Y', $dates[0])->format('Y-m-d');
                $end_date = Carbon::createFromFormat('d/m/Y', $dates[1])->format('Y-m-d') . ' 23:59:59';
                $queryTemplates->whereBetween('created_at', [$start_date, $end_date]);
            } else {
                $start_date = Carbon::createFromFormat('d/m/Y', $created_at)->format('Y-m-d');
                $queryTemplates->whereDate('created_at', '=', $start_date);
            }
        }

        $templates = $queryTemplates->orderBy('updated_at')->paginate(10);
        /**
         * END TEMPLATES QUERY
         */

        /**
         * START SURVEYS QUERY
         */
        $query = Survey::query();
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

        /*
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
        */

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

        $data = $query->orderBy('updated_at')->paginate(10);
        /**
         * END SURVEYS QUERY
         */

        $getSurveyRecurringTranslations = SurveyTemplates::getSurveyRecurringTranslations();

        $getSurveyStatusTranslations = Survey::getSurveyStatusTranslations();

        $getAuthorizedCompanies = getAuthorizedCompanies();

        $dateRange = Survey::getSurveysDateRange();
        $firstDate = $dateRange['first_date'];
        $lastDate = $dateRange['last_date'];

        return view('surveys.index', compact(
            'data',
            'templates',
            'getSurveyRecurringTranslations',
            'getSurveyStatusTranslations',
            'getAuthorizedCompanies',
            'firstDate',
            'lastDate',
        ));
    }

    public function show($id = null)
    {
        $data = Survey::findOrFail($id);

        if (!$data) {
            abort(404);
        }

        return view('surveys.show', compact('data') );
    }

    // Add
    public function create()
    {
        // Cache::flush();

        session()->forget('success');

        $data = null;

        $users = getUsers();

        $userId = auth()->id();

        $getAuthorizedCompanies = getAuthorizedCompanies();

        $getSurveyRecurringTranslations = SurveyTemplates::getSurveyRecurringTranslations();

        $queryTemplates = SurveyTemplates::query();
        $queryTemplates->where('user_id', $userId);
        $templates = $queryTemplates->orderBy('title')->paginate(50);

        return view('surveys.create', compact(
                'data',
                'templates',
                'users',
                'getAuthorizedCompanies',
                'getSurveyRecurringTranslations'
            )
        );
    }

    public function edit(Request $request, $id = null)
    {
        // Cache::flush();

        session()->forget('success');

        if (!$id) {
            abort(404);
        }

        $data = Survey::findOrFail($id);

        if (!$data) {
            abort(404);
        }

        $userId = auth()->id();

        $users = getUsers();

        $getAuthorizedCompanies = getAuthorizedCompanies();

        $getSurveyRecurringTranslations = SurveyTemplates::getSurveyRecurringTranslations();

        $queryTemplates = SurveyTemplates::query();
        $queryTemplates->where('user_id', $userId);
        $templates = $queryTemplates->orderBy('title')->paginate(50);

        return view('surveys.edit', compact(
                'data',
                'templates',
                'users',
                'getAuthorizedCompanies',
                'getSurveyRecurringTranslations'
            )
        );
    }

    public function storeOrUpdate(Request $request, $id = null)
    {
        // Cache::flush();

        $validatedData = $request->validate([
            //'title' => 'required|string|max:191',
            //'recurring' => 'required|in:once,daily,weekly,biweekly,monthly,annual',
            'start_date' => 'nullable|date_format:d/m/Y',
            //'description' => 'nullable|string|max:1000',

            'template_id' => 'required',
            //'delegated_to' => 'required',
            //'audited_by' => 'required',
            'distributed_data' => 'required',
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

        $jsonString = $request->input('distributed_data');
        $distributed_data = $jsonString ? json_decode($jsonString, true) : null;
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['success' => false, 'message' => 'Invalid JSON format']);
        }
        //$validatedData['distributed_data'] = $distributed_data ?? null;

        $distributed_data = $validatedData['distributed_data'];

        /*
        $jsonString = $request->input('delegated_to');
        $distributed_data = $jsonString ? json_decode($jsonString, true) : null;
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['success' => false, 'message' => 'Invalid JSON format']);
        }
        $validatedData['delegated_to'] = $distributed_data ?? null;


        $jsonString = $request->input('audited_by');
        $distributed_data = $jsonString ? json_decode($jsonString, true) : null;
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['success' => false, 'message' => 'Invalid JSON format']);
        }
        $validatedData['audited_by'] = $distributed_data ?? null;
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
                'message' => 'Vistoria atualizada!',
                'id' => $survey->id,
                'json' => $distributed_data
            ]);
        } else {
            // Store operation
            $survey = new Survey;
            $survey->fill($validatedData);
            $survey->save();

            return response()->json([
                'success' => true,
                'message' => 'Vistoria salva!',
                'id' => $survey->id,
                'json' => $distributed_data
            ]);
        }
    }
}
