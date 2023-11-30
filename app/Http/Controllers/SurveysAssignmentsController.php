<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Survey;
use App\Models\SurveyStep;
use Illuminate\Http\Request;
use App\Models\SurveyTemplates;
use Illuminate\Support\Facades\DB;
use App\Models\SurveyAssignments;

class SurveysAssignmentsController extends Controller
{
    public function show(Request $request, $assignmentId = null)
    {
        if (!$assignmentId) {
            abort(404);
        }

        $assignmentData = SurveyAssignments::findOrFail($assignmentId) ?? null;

        $surveyId = $assignmentData->survey_id;

        $surveyData = Survey::findOrFail($surveyId);

        $decodedData = isset($surveyData->template_data) ? json_decode($surveyData->template_data, true) : $surveyData->template_data;
        $reorderingData = SurveyTemplates::reorderingData($decodedData);
        $templateData = $reorderingData;

        $stepsWithTopics = SurveyStep::with(['topics' => function($query) {
                $query->orderBy('topic_order');
            }])
            ->where('survey_id', $surveyId)
            ->orderBy('step_order')
            ->get()
            ->map(function ($step) {
                return [
                    'id' => $step->id,
                    'survey_id' => $step->survey_id,
                    'step_id' => $step->id,
                    'step_order' => $step->step_order,
                    'term_id' => $step->term_id,
                    'topics' => $step->topics->map(function ($topic) {
                        return [
                            'topic_id' => $topic->id,
                            'question' => $topic->question
                        ];
                    })
                ];
            });
        $stepsWithTopics = json_decode($stepsWithTopics, true);

        return view('surveys.assignment.show', compact(
            'surveyData',
            'templateData',
            'assignmentData',
            'stepsWithTopics'
        ) );
    }

    public function formSurveyorAssignment(Request $request, $assignmentId)
    {
        if (!$assignmentId) {
            abort(404);
        }

        $assignmentData = SurveyAssignments::findOrFail($assignmentId) ?? null;

        $surveyId = $assignmentData->survey_id;

        $surveyData = Survey::findOrFail($surveyId);

        $decodedData = isset($surveyData->template_data) ? json_decode($surveyData->template_data, true) : $surveyData->template_data;
        $reorderingData = SurveyTemplates::reorderingData($decodedData);
        $templateData = $reorderingData;

        $stepsWithTopics = SurveyStep::with(['topics' => function($query) {
                $query->orderBy('topic_order');
            }])
            ->where('survey_id', $surveyId)
            ->orderBy('step_order')
            ->get()
            ->map(function ($step) {
                return [
                    'id' => $step->id,
                    'survey_id' => $step->survey_id,
                    'step_id' => $step->id,
                    'step_order' => $step->step_order,
                    'term_id' => $step->term_id,
                    'topics' => $step->topics->map(function ($topic) {
                        return [
                            'topic_id' => $topic->id,
                            'question' => $topic->question
                        ];
                    })
                ];
            });
        $stepsWithTopics = json_decode($stepsWithTopics, true);

        return view('surveys.assignment.form-surveyor', compact(
            'surveyData',
            'templateData',
            'assignmentData',
            'stepsWithTopics'
        ));
    }

    public function formAuditorAssignment(Request $request, $assignmentId)
    {
        if (!$assignmentId) {
            abort(404);
        }

        $assignmentData = SurveyAssignments::findOrFail($assignmentId) ?? null;

        $surveyId = $assignmentData->survey_id;

        $surveyData = Survey::findOrFail($surveyId);

        $decodedData = isset($surveyData->template_data) ? json_decode($surveyData->template_data, true) : $surveyData->template_data;
        $reorderingData = SurveyTemplates::reorderingData($decodedData);
        $templateData = $reorderingData;

        $stepsWithTopics = SurveyStep::with(['topics' => function($query) {
                $query->orderBy('topic_order');
            }])
            ->where('survey_id', $surveyId)
            ->orderBy('step_order')
            ->get()
            ->map(function ($step) {
                return [
                    'id' => $step->id,
                    'survey_id' => $step->survey_id,
                    'step_id' => $step->id,
                    'step_order' => $step->step_order,
                    'term_id' => $step->term_id,
                    'topics' => $step->topics->map(function ($topic) {
                        return [
                            'topic_id' => $topic->id,
                            'question' => $topic->question
                        ];
                    })
                ];
            });
        $stepsWithTopics = json_decode($stepsWithTopics, true);

        return view('surveys.assignment.form-auditor', compact(
            'surveyData',
            'templateData',
            'assignmentData',
            'stepsWithTopics'
        ));
    }

    public function changeAssignmentSurveyorStatus(Request $request)
    {
        $currentUserId = auth()->id();

        $assignmentId = $request->input('assignment_id');
        $assignmentId = intval($assignmentId);

        $data = SurveyAssignments::findOrFail($assignmentId);

        if ($currentUserId != $data->surveyor_id) {
            return response()->json(['success' => false, 'message' => 'Você não possui autorização para prosseguir com a tarefa delegada a outra pessoa']);
        }

        $currentStatus = $data->surveyor_status;

        if($currentStatus == 'auditing'){
            return response()->json([
                'success' => false,
                'message' => 'Esta Vistoria já foi enviada para Auditoria e não poderá ser editada.',
            ]);
        }
        if($currentStatus == 'losted' ){
            return response()->json([
                'success' => false,
                'message' => 'O prazo expirou e esta Vistoria foi perdida. Por isso não poderá mais ser editada.',
            ]);
        }

        if($currentStatus == 'new'){
            // [if currentStatus is new] Change to pending.
            $newStatus = 'pending';

            $message = 'Formulário gerado com sucesso';
        }elseif($currentStatus == 'in_progress'){
            // [if currentStatus is in_progress] Change to auditing.
            $newStatus = 'auditing';

            $message = 'Dados enviados para Auditoria';
        }else{
            $message = 'Status inalterado';

            $newStatus = $currentStatus;
        }

        SurveyAssignments::changeSurveyorAssignmentStatus($assignmentId, $newStatus);

        return response()->json(['success' => true, 'message' => $message]);
    }

    public function changeAssignmentAuditorStatus(Request $request)
    {
        $currentUserId = auth()->id();

        $assignmentId = $request->input('assignment_id');
        $assignmentId = intval($assignmentId);

        $data = SurveyAssignments::findOrFail($assignmentId);

        if ($currentUserId != $data->auditor_id) {
            return response()->json(['success' => false, 'message' => 'Você não possui autorização para prosseguir com a tarefa delegada a outra pessoa']);
        }

        $currentStatus = $data->auditor_status;

        if($currentStatus == 'completed'){
            return response()->json([
                'success' => false,
                'message' => 'Esta Auditoria já foi finalizada não poderá mais ser editada.',
            ]);
        }
        if($currentStatus == 'losted' ){
            return response()->json([
                'success' => false,
                'message' => 'O prazo expirou e esta Auditoria foi perdida. Por isso não poderá mais ser editada.',
            ]);
        }

        if($currentStatus == 'new'){
            // [if currentStatus is new] Change to pending.
            $newStatus = 'pending';

            $message = 'Formulário gerado com sucesso';
        }
        elseif($currentStatus == 'in_progress'){
            // [if currentStatus is in_progress] Change to completed.
            $newStatus = 'completed';

            $message = 'Auditoria finalizada';
        }else{
            $message = 'Status inalterado';

            $newStatus = $currentStatus;
        }

        // Change auditor_status. So... if newStatus was 'completed', change the surveyor_status to
        SurveyAssignments::changeAuditorAssignmentStatus($assignmentId, $newStatus);

        return response()->json(['success' => true, 'message' => $message]);
    }


}
