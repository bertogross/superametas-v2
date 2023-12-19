<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\AttachmentsController;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SurveyAssignments extends Model
{
    use HasFactory;

    protected $connection = 'smAppTemplate';

    public $timestamps = true;

    protected $fillable = ['survey_id', 'company_id', 'surveyor_id', 'auditor_id', 'surveyor_status', 'auditor_status'];

    // Start the task by distributing to each party
    public static function distributingAssignments($surveyId, $distributedData)
    {
        $distributedData = $distributedData ? json_decode($distributedData, true) : null;
        $distributedDataMerged = [];
        //foreach ($distributedData['auditor_id'] as $audited) {
            foreach ($distributedData['surveyor_id'] as $delegated) {
                //if ($audited['company_id'] === $delegated['company_id']) {
                    $distributedDataMerged[] = [
                        'company_id' => $delegated['company_id'],
                        //'auditor_id' => $audited['user_id'],
                        'surveyor_id' => $delegated['user_id']
                    ];
                    //break;
                //}
            }
        //}

        // Get the most recent date of assignment for the specific survey and remove
        SurveyAssignments::removeDistributingAssignments($surveyId);

        // Populate/repopulate = depends on are or not completed indivisual user tasks
        foreach ($distributedDataMerged as $value) {
            // Check if this surveyor_id has recent completed task
            $recentlySurveyorAssignment = DB::connection('smAppTemplate')->table('survey_assignments')
                ->where('surveyor_id', $value['surveyor_id'])
                ->where('survey_id', $surveyId)
                ->where('company_id', $value['company_id'])
                ->whereIn('surveyor_status', ['completed'])
                    ->max(DB::raw('DATE(created_at)'));

            // If user dont have completed task, populate
            if(!$recentlySurveyorAssignment){
                $data = [
                    'surveyor_id' => intval($value['surveyor_id']),
                    //'auditor_id' => intval($value['auditor_id']),
                    'survey_id' => intval($surveyId),
                    'company_id' => intval($value['company_id']),
                ];

                try {
                    $assignment = new self;
                    $assignment->fill($data);
                    $assignment->save();
                } catch (\Exception $e) {
                    // TODO
                    // Handle the exception or log it
                }
            }
        }
    }

    // Get the most recent date of assignment for the specific survey
    public static function removeDistributingAssignments($surveyId)
    {
        $lastDate = DB::connection('smAppTemplate')->table('survey_assignments')
            ->where('survey_id', $surveyId)
            ->whereIn('surveyor_status', ['new', 'pending', 'in_progress', ''])
            ->max(DB::raw('DATE(created_at)'));

        if ($lastDate) {
            // Fetch the assignments to be deleted
            $assignments = SurveyAssignments::whereDate('created_at', $lastDate)
                ->where('survey_id', $surveyId)
                ->whereIn('surveyor_status', ['new', 'pending', 'in_progress', ''])
                ->get();

            $assignmentIds = $assignments->pluck('id');

            // Find and delete attachments
            foreach ($assignments as $assignment) {
                // Extract attachment IDs from JSON columns
                $attachmentIdsSurvey = json_decode($assignment->attachments_survey, true) ?? [];
                $attachmentIdsAudit = json_decode($assignment->attachments_audit, true) ?? [];
                $allAttachmentIds = array_merge($attachmentIdsSurvey, $attachmentIdsAudit);

                $allAttachmentIds = array_filter($allAttachmentIds);

                // Delete each attachment
                if($allAttachmentIds && is_array($allAttachmentIds)){
                    foreach ($allAttachmentIds as $attachmentId) {
                        AttachmentsController::deletePhoto(null, $attachmentId);
                    }
                }

                // Delete the assignment record
                $assignment->delete();
            }

            // Delete related responses
            SurveyResponse::whereIn('assignment_id', $assignmentIds)->delete();
        }
    }

    public static function countSurveyAssignmentBySurveyId($surveyId)
    {
        return SurveyAssignments::where('survey_id', $surveyId)
            ->whereIn('surveyor_status', ['completed'])
            ->count();
    }

    public static function getAssignmentDelegatedsBySurveyId($surveyId)
    {
        $survey = Survey::findOrFail($surveyId);

        $surveyorIds = $auditorIds = [];

        // First, find on the distributedData because the survey can be not started yet
        $distributedData = $survey->distributed_data ?? null;
        if ($distributedData) {
            $decodedData = json_decode($distributedData, true);

            $surveyorData = $decodedData['surveyor_id'] ?? [];
            $auditorData = $decodedData['auditor_id'] ?? [];
        }

        // Second, get from assignments
        $surveyorQuery = SurveyAssignments::where('survey_id', $surveyId)
            ->select('surveyor_id AS user_id','company_id')
            ->get()
            ->toArray();

        $auditorQuery = SurveyAssignments::where('survey_id', $surveyId)
            ->select('auditor_id AS user_id','company_id')
            ->get()
            ->toArray();

        // Merge
        $surveyorMerged = array_merge($surveyorData, $surveyorQuery);
        $auditorMerged = array_merge($auditorData, $auditorQuery);

        // Remove duplicates
        $surveyorResult = array_values(array_intersect_key($surveyorMerged, array_unique(array_map(function($item) {
            return $item['user_id'] . '-' . $item['company_id'];
        }, $surveyorMerged))));

        $auditorResult = array_values(array_intersect_key($auditorMerged, array_unique(array_map(function($item) {
            return $item['user_id'] . '-' . $item['company_id'];
        }, $auditorMerged))));

        return [
            'surveyors' => $surveyorResult ?? null,
            'auditors' => $auditorResult ?? null
        ];
    }

    /*public static function getSurveysDelegatedsByUserId($userId)
    {
        return SurveyAssignments::where('surveyor_id', $userId)
            ->orWhere('auditor_id', $userId)
            ->select('survey_id')
            ->get()
            ->toArray();
    }*/

    public static function changeSurveyorAssignmentStatus($assignmentId, $status)
    {
        $data = SurveyAssignments::findOrFail($assignmentId);

        $surveyorId = $data->surveyor_id;
        $surveyId = $data->survey_id;
        $companyId = $data->company_id;

        $currentAuditorStatus = $data->auditor_status;

        if($status == 'pending'){
            // Field survey status column
            DB::connection('smAppTemplate')->table('surveys')
                ->where('id', $surveyId)
                ->update([
                    'status' => 'started',
                ]);
        }elseif($status == 'completed' && $currentAuditorStatus == 'waiting'){
            $columns['auditor_status'] = 'new';
        }
        /*elseif($status == 'auditing'){
            $columns['auditor_status'] = 'new';
            $data->update($columns);
        }*/

        // new status
        $columns['surveyor_status'] = $status;

        $data->update($columns);
    }

    public static function changeAuditorAssignmentStatus($assignmentId, $status)
    {
        $data = SurveyAssignments::findOrFail($assignmentId);

        $surveyorId = $data->surveyor_id;
        $surveyId = $data->survey_id;
        $companyId = $data->company_id;

        if($status == 'completed'){
            // If newStatus was 'completed', change the surveyor_status
            $column['surveyor_status'] = $status;
            $data->update($column);
        } elseif($status == 'in_progress'){
            // If newStatus was 'completed', change the surveyor_status
            $column['surveyor_status'] = 'auditing';
            $data->update($column);
        }

        // Change auditor_status
        $column['auditor_status'] = $status;
        $data->update($column);

    }

    public static function getAssignmentDateRange()
    {
        $firstDate = DB::connection('smAppTemplate')->table('survey_assignments')
            ->select(DB::raw('DATE_FORMAT(MIN(created_at), "%Y-%m-%d") as first_date'))
            ->first();

        $lastDate = DB::connection('smAppTemplate')->table('survey_assignments')
            ->select(DB::raw('DATE_FORMAT(MAX(created_at), "%Y-%m-%d") as last_date'))
            ->first();

        return [
            'first_date' => $firstDate->first_date ?? date('Y-m-d'),
            'last_date' => $lastDate->last_date ?? date('Y-m-d'),
        ];
    }

    public static function calculateSurveyPercentage($surveyId, $companyId, $assignmentId, $surveyorId, $auditorId, $designated)
    {
        // Assuming you have a method to count the total number of topics/questions in a survey
        $totalTopics = SurveyTopic::countSurveyTopics($surveyId);

        $countSurveyAuditor = SurveyResponse::countSurveyAuditorResponses($auditorId, $surveyId, $assignmentId);
        $countSurveySurveyor = SurveyResponse::countSurveySurveyorResponses($surveyorId, $surveyId, $assignmentId);

        if($auditorId === $surveyId){
            $countResponses = ($countSurveySurveyor + $countSurveyAuditor) / 2;
        }elseif($designated == 'auditor'){
            $countResponses = $countSurveyAuditor;
        }elseif($designated == 'surveyor'){
            $countResponses = $countSurveySurveyor;
        }else{
            $countResponses = ($countSurveySurveyor + $countSurveyAuditor) / 2;
        }

        // Calculate the percentage
        $percentage = 0;
        if ($totalTopics > 0) {
            $percentage = ($countResponses / $totalTopics) * 100;
        }

        return $percentage ? number_format($percentage, 0) : 0;
    }

    public static function getSurveyAssignmentStatusTranslations()
    {
        return [
             'new' => [
                'label' => 'Nova',
                'reverse' => 'Iniciar',
                'description' => 'Tarefas não inicializadas',
                'icon' => 'ri-play-fill',
                'color' => 'primary'
            ],
            /*'waiting' => [
                'label' => 'Aguardando',
                'reverse' => '',
                'description' => 'Aguardando a finalização da primeira etapa, Vistoria',
                'icon' => 'ri-pause-mini-line',
                'color' => 'primary'
            ],*/
            'pending' => [
                'label' => 'Pendente',
                'reverse' => 'Abrir Formulário',
                'description' => 'Tarefas inicializadas',
                'icon' => 'ri-survey-line',
                'color' => 'warning'
            ],
            'in_progress' => [
                'label' => 'Em Progresso',
                'reverse' => 'Retomar Atividade',
                'description' => 'Tarefas sendo executadas',
                'icon' => 'ri-todo-fill',
                'color' => 'info'
            ],
            'completed' => [
                'label' => 'Concluída',
                'reverse' => '',
                'description' => 'Tarefas que foram concluídas',
                'icon' => 'ri-check-double-fill',
                'color' => 'success'
            ],
            'auditing' => [
                'label' => 'Em Auditoria',
                'reverse' => 'Abrir Formulário',
                'description' => 'Tarefas sendo auditadas',
                'icon' => 'ri-todo-line',
                'color' => 'secondary'
            ],
            'losted' => [
                'label' => 'Perdida',
                'reverse' => '',
                'description' => 'Tarefas não concluídas no prazo',
                'icon' => 'ri-skull-line',
                'color' => 'danger'
            ],
            'bypass' => [
                'label' => 'Ignorado',
                'reverse' => '',
                'description' => 'Tarefa ignorada',
                'icon' => 'ri-skull-line',
                'color' => 'danger'
            ]
        ];
    }

    // Get a descriptive label title based on the task status and roles involved
    public static function getSurveyAssignmentLabelTitle($surveyorStatus, $auditorStatus)
    {
        if ($surveyorStatus == 'completed' && $auditorStatus == 'completed') {
            return 'A <u>Vistoria</u> e a <u>Auditoria</u> foram efetuadas';
        } elseif ($surveyorStatus == 'completed' && $auditorStatus != 'completed') {
            return 'A <u>Vistoria</u> foi concluída';
        } elseif ($surveyorStatus != 'completed' && $auditorStatus == 'completed') {
            return 'A <u>Auditoria</u> foi concluída';
        } else {
            return 'Tarefa';
        }
    }

    public static function getSurveyAssignmentDeadline($recurring, $assignmentCreatedAt)
    {
        // Ensure that $assignmentCreatedAt is a Carbon instance
        if (!$assignmentCreatedAt instanceof \Carbon\Carbon) {
            $assignmentCreatedAt = Carbon::parse($assignmentCreatedAt);
        }

        switch ($recurring) {
            case 'once':
            case 'daily':
                return $assignmentCreatedAt ? $assignmentCreatedAt->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY') : '-';
                break;
            case 'weekly':
                return $assignmentCreatedAt->addWeek()->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY');
                break;
            case 'biweekly':
                return $assignmentCreatedAt->addWeeks(2)->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY');
                break;
            case 'monthly':
                return $assignmentCreatedAt->addMonthNoOverflow()->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY');
                break;
            case 'annual':
                return $assignmentCreatedAt->addYear()->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY');
                break;
            default:
                return '-';
                break;
        }
    }

    public static function countSurveyAssignmentSurveyorTasks($profileUserId, $filteredStatuses){
        $filteredStatuses = array_keys($filteredStatuses);

        return SurveyAssignments::where('surveyor_id', $profileUserId)
            ->whereIn('surveyor_status', $filteredStatuses)
            ->count();
    }

    public static function countSurveyAssignmentAuditorTasks($profileUserId, $filteredStatuses){
        $filteredStatuses = array_keys($filteredStatuses);

        return SurveyAssignments::where('auditor_id', $profileUserId)
            ->whereIn('auditor_status', $filteredStatuses)
            ->count();
    }




}
