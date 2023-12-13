<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        //foreach ($distributedData['audited_by'] as $audited) {
            foreach ($distributedData['delegated_to'] as $delegated) {
                //if ($audited['company_id'] === $delegated['company_id']) {
                    $distributedDataMerged[] = [
                        'company_id' => $delegated['company_id'],
                        //'auditor_id' => $audited['user_id'],
                        'surveyor_id' => $delegated['user_id']
                    ];
                    break;
                //}
            }
        //}

        // Delete all data where created_at is equal to today
        $today = now()->startOfDay(); // Get the start of today
        SurveyAssignments::whereDate('created_at', $today)->where('survey_id', $surveyId)->delete();

        foreach ($distributedDataMerged as $value) {
            $data = [
                'surveyor_id' => intval($value['surveyor_id']),
                //'auditor_id' => intval($value['auditor_id']),
                'survey_id' => intval($surveyId),
                'company_id' => intval($value['company_id']),
            ];

            try {
                $assignment = new SurveyAssignments;
                $assignment->fill($data);
                $assignment->save();
            } catch (\Exception $e) {
                // TODO
                // Handle the exception or log it
            }
        }
    }

    public static function changeSurveyorAssignmentStatus($assignmentId, $status)
    {
        $data = SurveyAssignments::findOrFail($assignmentId);

        $surveyorId = $data->surveyor_id;
        $surveyId = $data->survey_id;
        $companyId = $data->company_id;

        if($status == 'pending'){
            // Field survey status column
            DB::connection('smAppTemplate')->table('surveys')
                ->where('id', $surveyId)
                ->update([
                    'status' => 'started',
                ]);
        }elseif($status == 'completed'){
            $column['auditor_status'] = 'new';
            $data->update($column);
        }elseif($status == 'auditing'){
            $column['auditor_status'] = 'new';
            $data->update($column);
        }

        // new status
        $column['surveyor_status'] = $status;
        $data->update($column);
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

        $countSurveyAuditor = SurveyResponse::countSurveyAuditorResponses($auditorId, $surveyId, $companyId, $assignmentId);
        $countSurveySurveyor = SurveyResponse::countSurveySurveyorResponses($surveyorId, $surveyId, $companyId, $assignmentId);

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
            'completed' => [
                'label' => 'Concluída',
                'reverse' => '',
                'description' => 'Tarefas que foram concluídas',
                'icon' => 'ri-check-double-fill',
                'color' => 'success'
            ],
            /*'waiting' => [
                'label' => 'Aguardando',
                'reverse' => '',
                'description' => 'Aguardando a finalização da primeira etapa, Checklist',
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
                'reverse' => 'Abrir Formulário',
                'description' => 'Tarefas em andamento',
                'icon' => 'ri-time-line',
                'color' => 'info'
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
            ]
        ];
    }

    // Get a descriptive label title based on the task status and roles involved
    public static function getSurveyAssignmentLabelTitle($surveyorStatus, $auditorStatus)
    {
        if ($surveyorStatus == 'completed' && $auditorStatus == 'completed') {
            return 'A <u>Checklist</u> e a <u>Auditoria</u> foram efetuadas';
        } elseif ($surveyorStatus == 'completed' && $auditorStatus != 'completed') {
            return 'A <u>Checklist</u> foi concluída';
        } elseif ($surveyorStatus != 'completed' && $auditorStatus == 'completed') {
            return 'A <u>Auditoria</u> foi concluída';
        } else {
            return 'Tarefa em andamento';
        }
    }

    // Get a descriptive title for a date based on the task status
    public static function getSurveyAssignmentDateTitleByKey($statusKey){
        switch ($statusKey) {
            case 'completed':
                return 'A data em que esta tarefa foi desempenhada';
            case 'losted':
                return 'A data em que esta tarefa deveria ter sido desempenhada';
            default:
                return 'A data em que esta tarefa deverá ser desempenhada';
        }
    }

}
