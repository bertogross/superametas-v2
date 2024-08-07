<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Survey;
use App\Models\SurveyStep;
use App\Models\SurveyTerms;
use Illuminate\Http\Request;
use App\Models\SurveyTemplates;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SurveysTemplatesController extends Controller
{
    protected $connection = 'smAppTemplate';

    public function preview(Request $request, $id = null)
    {
        //Cache::flush();

        if (!$id) {
            abort(404);
        }

        $data = SurveyTemplates::findOrFail($id);

        $reorderingData = SurveyTemplates::reorderingData($data);
        $stepsWithTopics = $reorderingData ?? null;

        $preview = $request->query('preview', false);

        $edition = $request->query('edition', false);

        return view('surveys.templates.preview', compact(
            'data',
            'stepsWithTopics',
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

        $data = [];

        $surveysCount = 0;

        $defaultOriginal = getWarehouseTerms();

        $result = array_filter($defaultOriginal);

        return view('surveys.templates.create', compact(
                'data',
                'result',
                'terms',
                'surveysCount'
            )
        );
    }

    public function edit(Request $request, $id = null)
    {
        //Cache::flush();

        if (!$id) {
            abort(404);
        }

        session()->forget('success');

        $currentUserId = auth()->id();

        $data = SurveyTemplates::findOrFail($id);

        $surveysCount = Survey::where('template_id', $id)
            //->where('user_id', $currentUserId)
            ->count();

        $terms = SurveyTerms::all();

        $reorderingData = SurveyTemplates::reorderingData($data);
        //$result = $reorderingData ?? null;

        $custom = SurveyTemplates::getByType($reorderingData, 'custom');
        $custom = $custom ?? [];

        $default = SurveyTemplates::getByType($reorderingData, 'default');
        $default = $default ?? [];

        /*
        // DEPRECATED ?
        // TODO think about if is realy necessary add new original data to registered template. Are a conflict here related with the stepData new_position field.
        $defaultOriginal = getWarehouseTerms();
        $default = SurveyTemplates::mergeTemplateDataArrays($defaultOriginal, $default);
        */

        $result = array_merge($default, $custom);

        return view('surveys.templates.edit', compact(
                'data',
                'surveysCount',
                'result',
                'terms',
            )
        );
    }

    public function storeOrUpdate(Request $request, $id = null)
    {
        // Cache::flush();
        $currentUserId = auth()->id();

        $messages = [
            'title.required' => 'Informe o título',
            'title.max' => 'Título deve possuir no máximo 190 caracteres.',
            'description.required' => 'Descreva',
            'description.max' => 'A descrição deve conter do máximo 500 caracteres.',
        ];

        $validatedData = $request->validate([
            'title' => 'required|string|max:191',
            'description' => 'nullable|string|max:500',
            'template_data' => 'required',
        ], $messages);

        // Convert array inputs to JSON strings for storage
        $validatedData = array_map(function ($value) {
            return is_array($value) ? json_encode($value) : $value;
        }, $validatedData);

        $templateData = $validatedData['template_data'];

        $validatedData['user_id'] = $currentUserId;

        if ($id) {
            // Update operation
            $templates = SurveyTemplates::findOrFail($id);

            // Check if the current user is the creator
            if ($currentUserId != $templates->user_id) {
                return response()->json(['success' => false, 'message' => 'Você não possui autorização para editar um registro gerado por outra pessoa']);
            }

            $templates->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Modelo atualizado!',
                'id' => $templates->id,
                'json' => $templateData
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
                'json' => $templateData
            ]);
        }
    }


}
