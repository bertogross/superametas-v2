<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Survey;
use App\Models\SurveyStep;
use App\Models\SurveyTopic;
use Illuminate\Http\Request;
use App\Models\SurveyResponse;
use App\Models\SurveyTemplates;
use App\Models\SurveyAssignments;
use Illuminate\Support\Facades\DB;

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

        $reorderingData = SurveyTemplates::reorderingData($surveyData);
        $templateData = $reorderingData ?? null;

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
        $stepsWithTopics = $stepsWithTopics ? json_decode($stepsWithTopics, true) : null;


        $analyticTermsData = Survey::fetchAndTransformSurveyDataByTerms($surveyId, $assignmentId);

        /**
         * START get terms
         */
        $terms = [];
        /*$departmentsQuery = DB::connection('smAppTemplate')
            ->table('wlsm_departments')
            ->get()
            ->toArray();
        foreach ($departmentsQuery as $department) {
            $terms[$department->department_id] = [
                'name' => strtoupper($department->department_alias),
            ];
        }*/
        $wharehouseTermsQuery = DB::connection('smWarehouse')
            ->table('survey_terms')
            ->get()
            ->toArray();
        foreach ($wharehouseTermsQuery as $term) {
            $terms[$term->id] = [
                'name' => strtoupper($term->name),
            ];
        }
        
        $termsQuery = DB::connection('smAppTemplate')
            ->table('survey_terms')
            ->get()
            ->toArray();
        foreach ($termsQuery as $term) {
            $terms[$term->id] = [
                'name' => strtoupper($term->name),
            ];
        }
        /**
         * END get terms
         */

        return view('surveys.assignment.show', compact(
            'surveyData',
            'templateData',
            'assignmentData',
            'stepsWithTopics',
            'analyticTermsData',
            'terms'
        ) );
    }

    public function formSurveyorAssignment(Request $request, $assignmentId)
    {
        if (!$assignmentId) {
            abort(404);
        }

        $currentUserId = auth()->id();

        $assignmentData = SurveyAssignments::findOrFail($assignmentId) ?? null;

        $surveyId = $assignmentData->survey_id;

        $companyId = $assignmentData->company_id;

        $surveyData = Survey::findOrFail($surveyId);

        $reorderingData = SurveyTemplates::reorderingData($surveyData);
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
        $stepsWithTopics = $stepsWithTopics ? json_decode($stepsWithTopics, true) : null;

        $countTopics = SurveyTopic::countSurveyTopics($surveyId);

        $countResponses = SurveyResponse::countSurveySurveyorResponses($currentUserId, $surveyId, $companyId, $assignmentId);

        $percentage = $countResponses > 0 ? ($countResponses / $countTopics) * 100 : 0;
        $percentage = number_format($percentage, 0);

        return view('surveys.assignment.form-surveyor', compact(
            'surveyData',
            'templateData',
            'assignmentData',
            'stepsWithTopics',
            'percentage'
        ));
    }

    public function formAuditorAssignment(Request $request, $assignmentId)
    {
        if (!$assignmentId) {
            abort(404);
        }

        $currentUserId = auth()->id();

        $assignmentData = SurveyAssignments::findOrFail($assignmentId) ?? null;

        $surveyId = $assignmentData->survey_id;

        $companyId = $assignmentData->company_id;

        $surveyData = Survey::findOrFail($surveyId);

        $reorderingData = SurveyTemplates::reorderingData($surveyData);
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
        $stepsWithTopics = $stepsWithTopics ? json_decode($stepsWithTopics, true) : null;

        $countTopics = SurveyTopic::countSurveyTopics($surveyId);

        $countResponses = SurveyResponse::countSurveySurveyorResponses($currentUserId, $surveyId, $companyId, $assignmentId);

        $percentage = $countResponses > 0 ? ($countResponses / $countTopics) * 100 : 0;
        $percentage = number_format($percentage, 0);

        return view('surveys.assignment.form-auditor', compact(
            'surveyData',
            'templateData',
            'assignmentData',
            'stepsWithTopics',
            'percentage'
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
                'message' => 'Esta Tarefa já foi finalizada e não poderá ser editada.',
            ]);
        }
        if($currentStatus == 'losted' ){
            return response()->json([
                'success' => false,
                'message' => 'O prazo expirou e esta Tarefa foi perdida. Por isso não poderá mais ser editada.',
            ]);
        }

        if($currentStatus == 'new'){
            // [if currentStatus is new] Change to pending.
            $newStatus = 'pending';

            $message = 'Formulário gerado com sucesso';
        }elseif($currentStatus == 'in_progress'){
            // [if currentStatus is in_progress] Change to auditing.
            //$newStatus = 'auditing';
            $newStatus = 'completed';

            $message = 'Dados gravados';
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
                'message' => 'Esta Tarefa já foi finalizada não poderá mais ser editada.',
            ]);
        }
        if($currentStatus == 'losted' ){
            return response()->json([
                'success' => false,
                'message' => 'O prazo expirou, esta Tarefa foi perdida e por isso não poderá mais ser editada.',
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

            $message = 'Tarefa finalizada';
        }else{
            $message = 'Status inalterado';

            $newStatus = $currentStatus;
        }

        // Change auditor_status. So... if newStatus was 'completed', change the surveyor_status to
        SurveyAssignments::changeAuditorAssignmentStatus($assignmentId, $newStatus);

        return response()->json(['success' => true, 'message' => $message]);
    }

    public function getRecentActivities()
    {
        $today = Carbon::today();

        $surveyorArrStatus = ['pending', 'in_progress', 'auditing', 'completed'];

        $auditorArrStatus = ['in_progress', 'completed']; //'waiting', 'pending',

        // Fetching surveyor assignments
        $surveyorAssignments = SurveyAssignments::whereIn('surveyor_status', $surveyorArrStatus)
                                                ->whereDate('created_at', '=', $today)
                                                ->orderBy('updated_at', 'desc')
                                                ->limit(100)
                                                ->get();

        // Fetching auditor assignments
        $auditorAssignments = SurveyAssignments::whereIn('auditor_status', $auditorArrStatus)
                                            ->whereDate('created_at', '=', $today)
                                            ->orderBy('updated_at', 'desc')
                                            ->limit(100)
                                            ->get();

        if($surveyorAssignments){

            $activities = [];

            // Process surveyor assignments
            foreach ($surveyorAssignments as $assignment) {
                $activities[] = $this->processAssignment($assignment, 'surveyor');
            }

            // Process auditor assignments
            foreach ($auditorAssignments as $assignment) {
                $activities[] = $this->processAssignment($assignment, 'auditor');
            }

            $activities = array_filter($activities);

            //return response()->json($activities);
            if(is_array($activities) && count($activities) > 0 ){
                return response()->json(['success' => true, 'activities' => $activities]);
            }else{
                return response()->json(['success' => false, 'message' => 'Ainda não há dados']);

            }
        }else{
            return response()->json(['success' => false, 'message' => 'Ainda não há dados']);
        }

    }

    private function processAssignment($assignment, $designated)
    {

        $assignmentId = $assignment->id;

        $surveyId = $assignment->survey_id;
        $survey = Survey::findOrFail($surveyId);
        $templateName = getSurveyTemplateNameById($survey->template_id);

        $companyId = $assignment->company_id;
        $companyName = getCompanyNameById($companyId);

        $surveyorId = $assignment->surveyor_id ?? null;
        $auditorId = $assignment->auditor_id ?? null;

        $assignmentStatus = $assignment->{$designated . '_status'} ?? null;

        $percentage = SurveyAssignments::calculateSurveyPercentage($surveyId, $companyId, $assignmentId, $surveyorId, $auditorId, $designated);
        $progressBarClass = getProgressBarClass($percentage);

        $label = $designated == 'surveyor' ? '<span class="badge bg-dark-subtle text-body badge-border">Checklist</span>'
                                        : '<span class="badge bg-dark-subtle text-secondary badge-border">Auditoria</span>';

        if($designated == 'auditor'){
            $designatedUserId = $auditorId;
        }elseif($designated == 'surveyor'){
            $designatedUserId = $surveyorId;
        }

        return [
            'assignmentId' => $assignmentId,
            'surveyId' => $surveyId,
            'companyId' => $companyId,
            'companyName' => limitChars($companyName, 20),
            'templateName' => limitChars($templateName, 26),
            'assignmentStatus' => $assignmentStatus,
            'designatedUserId' => $designatedUserId,
            'designatedUserName' => limitChars(getUserData($designatedUserId)['name'], 20),
            'designatedUserAvatar' => getUserData($designatedUserId)['avatar'],
            'designatedUserProfileURL' => route('profileShowURL', $designatedUserId),
            'label' => $label,
            'percentage' => $percentage,
            'progressBarClass' => $progressBarClass,
            'createddAt' => $assignment->created_at->toDateTimeString(),
            'updatedAt' => $assignment->updated_at->toDateTimeString()
        ];
    }




}
