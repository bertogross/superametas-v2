<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\SurveyTemplates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SurveysTemplatesController extends Controller
{
    protected $connection = 'smAppTemplate';

    /*
    public function index(Request $request)
    {
        $created_at = $request->input('created_at');

        $query = SurveyTemplates::query();

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

        $getSurveyRecurringTranslations = SurveyTemplates::getSurveyRecurringTranslations();

        $surveyTemplates = $query->orderBy('updated_at')->paginate(10);

        return view('surveys.template.listing', compact(
            'surveyTemplates',
            'getSurveyRecurringTranslations'
        ));
    }
    */

    public function show(Request $request, $id = null)
    {
        if (!$id) {
            abort(404);
        }

        $data = [];

        $data = SurveyTemplates::findOrFail($id);

        if (!$data) {
            abort(404);
        }

        $decodedData = isset($data->jsondata) && is_string($data->jsondata) ? json_decode($data->jsondata, true) : $data->jsondata;

        $reorderingData = SurveyTemplates::reorderingData($decodedData);

        $custom = SurveyTemplates::getByType($reorderingData, 'custom');
        $custom = $custom ?? null;

        $default = SurveyTemplates::getByType($reorderingData, 'default');
        $default = $default ?? null;

        $getSurveyRecurringTranslations = SurveyTemplates::getSurveyRecurringTranslations();

        $preview = $request->query('preview', false);

        $edition = $request->query('edition', false);

        return view('surveys.template.show', compact(
            'data',
            'custom',
            'default',
            'preview',
            'edition',
            'getSurveyRecurringTranslations'
        ) );
    }


    // Add
    public function create()
    {
        // Cache::flush();

        session()->forget('success');

        $getAuthorizedCompanies = getAuthorizedCompanies();

        $getActiveDepartments = getActiveDepartments();

        $surveyTemplate = $custom = null;

        $default = [];
        foreach($getActiveDepartments as $index => $department){
            $default[$index] = [
                'stepData' => [
                    'step_name' => $department->department_alias,
                    'step_id' => $department->id,
                    'original_position' => $index,
                    'new_position' => $index
                ],
                //'topics' => $preListing
            ];
        }
        $default = array_filter($default);

        return view('surveys.template.create', compact(
                'surveyTemplate',
                'default',
                'custom'
            )
        );
    }

    public function edit(Request $request, $id = null)
    {
        // Cache::flush();

        if (!$id) {
            abort(404);
        }

        session()->forget('success');

        $data = [];

        $data = SurveyTemplates::findOrFail($id);

        if (!$data) {
            abort(404);
        }

        $decodedData = isset($data->jsondata) && is_string($data->jsondata) ? json_decode($data->jsondata, true) : $data->jsondata;

        $reorderingData = SurveyTemplates::reorderingData($decodedData);

        $custom = SurveyTemplates::getByType($reorderingData, 'custom');
        $custom = $custom ?? null;

        $default = SurveyTemplates::getByType($reorderingData, 'default');
        $default = $default ?? null;

        $getSurveyRecurringTranslations = SurveyTemplates::getSurveyRecurringTranslations();

        $view = view('surveys.template.edit', compact(
            'data',
            'custom',
            'default',
            'decodedData',
            'getSurveyRecurringTranslations'
            )
        );

        return $view;
    }

    public function storeOrUpdate(Request $request, $id = null)
    {
        // Cache::flush();

        $validatedData = $request->validate([
            'title' => 'required|string|max:191',
            'description' => 'nullable|string|max:1000',
            'jsondata' => 'required',
            'recurring' => 'required|in:once,daily,weekly,biweekly,monthly,annual',
        ]);

        // Convert array inputs to JSON strings for storage
        $validatedData = array_map(function ($value) {
            return is_array($value) ? json_encode($value) : $value;
        }, $validatedData);

        $jsondata = $validatedData['jsondata'];

        $userId = auth()->id();
        $validatedData['user_id'] = $userId;

        if ($id) {
            // Update operation
            $surveyTemplate = SurveyTemplates::findOrFail($id);

            // Check if the current user is the creator
            if ($userId != $surveyTemplate->user_id) {
                return response()->json(['success' => false, 'message' => 'You are not authorized to edit this survey.']);
            }

            $surveyTemplate->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Survey updated successfully!',
                'id' => $surveyTemplate->id,
                'json' => $jsondata
            ]);
        } else {
            // Store operation
            $surveyTemplate = new SurveyTemplates;
            $surveyTemplate->fill($validatedData);
            $surveyTemplate->save();

            return response()->json([
                'success' => true,
                'message' => 'Survey saved successfully!',
                'id' => $surveyTemplate->id,
                'json' => $jsondata
            ]);
        }
    }
}
