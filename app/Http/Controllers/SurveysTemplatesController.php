<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\SurveyTemplates;
use App\Models\SurveyStep;
use App\Models\SurveyTerms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SurveysTemplatesController extends Controller
{
    protected $connection = 'smAppTemplate';

    public function show(Request $request, $id = null)
    {
        if (!$id) {
            abort(404);
        }

        $data = SurveyTemplates::findOrFail($id);

        if (!$data) {
            abort(404);
        }

        $decodedData = isset($data->template_data) && is_string($data->template_data) ? json_decode($data->template_data, true) : $data->template_data;

        $reorderingData = SurveyTemplates::reorderingData($decodedData);

        $result = $reorderingData ?? null;

        $preview = $request->query('preview', false);

        $edition = $request->query('edition', false);

        return view('surveys.templates.show', compact(
            'data',
            'result',
            'preview',
            'edition',
        ) );
    }


    // Add
    public function create()
    {
        // Cache::flush();

        session()->forget('success');

        $terms = SurveyTerms::all();

        $getAuthorizedCompanies = getAuthorizedCompanies();

        $getActiveDepartments = getActiveDepartments();

        $data = $defaultOriginal = [];

        $index = 0;
        foreach($getActiveDepartments as $department){
            $defaultOriginal[] = [
                'stepData' => [
                    'step_name' => $department->department_alias,
                    'term_id' => $department->department_id,
                    'type' => 'default',
                    'original_position' => $department->department_id,
                    'new_position' => $index,
                ],
                //'topics' => $preListing
            ];
            $index++;
        }
        $result = array_filter($defaultOriginal);

        return view('surveys.templates.create', compact(
                'data',
                'result',
                'terms',
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

        $userId = auth()->id();

        $data = SurveyTemplates::findOrFail($id);
        /*
        $data = SurveyTemplates::query();
        $data->where('id', $id);
        $data->where('user_id', $userId);
        $data->get();
        */

        if (!$data) {
            abort(404);
        }

        $getActiveDepartments = getActiveDepartments();

        $terms = SurveyTerms::all();

        // Check if the current user is the creator
        /*
        if ($userId == $data->user_id) {
            return response()->json(['success' => false, 'message' => 'Você não possui autorização para editar um registro efetuado por outra pessoa']);
        }
        */

        $decodedData = isset($data->template_data) ? json_decode($data->template_data, true) : $data->template_data;

        $reorderingData = SurveyTemplates::reorderingData($decodedData);

        //$result = $reorderingData ?? null;

        $custom = SurveyTemplates::getByType($reorderingData, 'custom');
        $custom = $custom ?? [];

        $default = SurveyTemplates::getByType($reorderingData, 'default');
        $default = $default ?? [];

        $defaultOriginal = [];
        $index = 0;
        foreach($getActiveDepartments as  $department){
            $defaultOriginal[] = [
                'stepData' => [
                    'step_name' => $department->department_alias,
                    'term_id' => $department->department_id,
                    'type' => 'default',
                    'original_position' => $department->department_id,
                    'new_position' =>$index,
                ],
                //'topics' => $preListing
            ];
            $index++;
        }
        $defaultOriginal = array_filter($defaultOriginal);

        $default = SurveyTemplates::mergeTemplateDataArrays($defaultOriginal, $default);

        $result = array_merge($default, $custom);

        $view = view('surveys.templates.edit', compact(
                'data',
                'result',
                'terms',
            )
        );

        return $view;
    }

    public function storeOrUpdate(Request $request, $id = null)
    {
        // Cache::flush();
        $userId = auth()->id();

        $validatedData = $request->validate([
            'title' => 'required|string|max:191',
            'description' => 'nullable|string|max:1000',
            'template_data' => 'required',
        ]);

        // Convert array inputs to JSON strings for storage
        $validatedData = array_map(function ($value) {
            return is_array($value) ? json_encode($value) : $value;
        }, $validatedData);

        $template_data = $validatedData['template_data'];

        $validatedData['user_id'] = $userId;

        if ($id) {
            // Update operation
            $templates = SurveyTemplates::findOrFail($id);

            // Check if the current user is the creator
            if ($userId != $templates->user_id) {
                return response()->json(['success' => false, 'message' => 'Você não possui autorização para editar um registro efetuado por outra pessoa']);
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
