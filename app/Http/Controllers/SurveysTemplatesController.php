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

        $templates = $query->orderBy('updated_at')->paginate(10);

        return view('surveys.template.listing', compact(
            'templates',
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

        $decodedData = isset($data->template_data) && is_string($data->template_data) ? json_decode($data->template_data, true) : $data->template_data;

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

        $data = $custom = $templates = null;

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

        $getSurveyRecurringTranslations = SurveyTemplates::getSurveyRecurringTranslations();

        return view('surveys.template.create', compact(
                'data',
                'custom',
                'default',
                'templates',
                'getSurveyRecurringTranslations'
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

        $decodedData = isset($data->template_data) && is_string($data->template_data) ? json_decode($data->template_data, true) : $data->template_data;

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
            'template_data' => 'required',
            'recurring' => 'required|in:once,daily,weekly,biweekly,monthly,annual',
        ]);

        // Convert array inputs to JSON strings for storage
        $validatedData = array_map(function ($value) {
            return is_array($value) ? json_encode($value) : $value;
        }, $validatedData);

        $template_data = $validatedData['template_data'];

        $userId = auth()->id();
        $validatedData['user_id'] = $userId;

        if ($id) {
            // Update operation
            $templates = SurveyTemplates::findOrFail($id);

            // Check if the current user is the creator
            if ($userId != $templates->user_id) {
                return response()->json(['success' => false, 'message' => 'You are not authorized to edit this survey.']);
            }

            $templates->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Modelo atualizado!',
                'id' => $templates->id,
                'json' => $template_data
            ]);
        } else {
            // Store operation
            $templates = new SurveyTemplates;
            $templates->fill($validatedData);
            $templates->save();

            return response()->json([
                'success' => true,
                'message' => 'Modelo salvo!',
                'id' => $templates->id,
                'json' => $template_data
            ]);
        }
    }
}
