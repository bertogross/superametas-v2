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

    // Populate assignments
    public static function startSurveyAssignments($surveyId)
    {
        $today = Carbon::today();
        $survey = Survey::findOrFail($surveyId);

        SurveyAssignments::checkSurveyAssignmentUntilYesterday($survey->id);

        $startAt = $survey->start_at; // Date when the survey started
        $endIn = $survey->end_in; // The final date

        $status = $survey->status;
        $recurring = $survey->recurring;

        $distributedData = $survey->distributed_data ?? null;

        if ($distributedData && $status == 'started') {

            // Check if there are survey assignments for today
            $assignmentsCount = SurveyAssignments::where('survey_id', $surveyId)
                ->whereDate('created_at', '=', $today)
                ->count();

            // If there are no assignments for today, check the recurrence pattern
            if ($assignmentsCount == 0) {
                switch ($recurring) {
                    case 'once':
                        SurveyAssignments::distributingAssignments($surveyId);
                    break;
                    case 'daily':
                        SurveyAssignments::distributingAssignments($surveyId);
                    break;
                    case 'weekly':
                        // Calculate the day of the week for both $startAt and $today
                        $specificDayOfWeek = $startAt->dayOfWeek;
                        $currentDayOfWeek = $today->dayOfWeek;

                        if ($specificDayOfWeek === $currentDayOfWeek) {
                            SurveyAssignments::distributingAssignments($surveyId);
                        }
                    break;
                    case 'biweekly':
                        // Calculate 15 days after $startAt for biweekly recurrence
                        $biweeklyDate = $startAt->addDays(15);

                        // Check if today matches the calculated biweekly date
                        if ($today->equalTo($biweeklyDate)) {
                            SurveyAssignments::distributingAssignments($surveyId);
                        }
                    break;
                    case 'monthly':
                        // Check if $startAt matches the specific day of the month for monthly recurrence
                        $specificDayOfMonth = $startAt->day;

                        // Adjust the specificDay to a date that is safe within this month
                        if ($specificDayOfMonth > $today->daysInMonth) {
                            $specificDayOfMonth = $today->daysInMonth;
                        }

                        if ($today->day == $specificDayOfMonth) {
                            SurveyAssignments::distributingAssignments($surveyId);
                        }
                    break;
                    case 'annual':
                        // Check if $startAt matches the specific day and month for annual recurrence
                        $specificDay = $startAt->day;
                        $specificMonth = $startAt->month;

                        // Check if the $startAt date conflicts with the current month and year
                        if ($today->year == $startAt->year && $today->month == $specificMonth) {
                            // Adjust the specificDay to a date that is safe within this month
                            if ($specificDay > $today->daysInMonth) {
                                $specificDay = $today->daysInMonth; // Set it to the last day of the month
                            }
                        }

                        if ($today->day == $specificDay && $today->month == $specificMonth) {
                            SurveyAssignments::distributingAssignments($surveyId);
                        }
                    break;
                }
            }
        }
    }

    // Check the 'survey_assignments' table to see which tasks were not completed by yesterday and change the status to 'losted'
    public static function checkSurveyAssignmentUntilYesterday($surveyId)
    {
        $yesterday = Carbon::yesterday()->format('Y-m-d');

        // Get all survey assignments that were not completed by yesterday
        $assignments = SurveyAssignments::where('survey_id', $surveyId)
            ->whereDate('created_at', '<=', $yesterday)
            ->get();

        foreach ($assignments as $assignment) {
            if( in_array($assignment->surveyor_status, ['completed', 'auditing']) && !in_array($assignment->auditor_status, ['completed']) ){
                // Change auditor_status to 'bypass' and surveyor_status to 'completed'
                $assignment->auditor_status = 'bypass';
                $assignment->surveyor_status = 'completed';
            } elseif ( !in_array($assignment->surveyor_status, ['completed']) && !in_array($assignment->auditor_status, ['completed']) ) {
                // Change surveyor_status to 'losted' and auditor_status to 'losted'
                $assignment->auditor_status = 'bypass';
                $assignment->surveyor_status = 'losted';
            }

            $assignment->save();
        }
        /*
        foreach ($assignments as $assignment) {
            if ($assignment->surveyor_status === 'completed' || $assignment->surveyor_status === 'auditing') {
                if ($assignment->auditor_status !== 'completed') {
                    // Change auditor_status to 'bypass' and surveyor_status to 'completed'
                    $assignment->auditor_status = 'bypass';
                    $assignment->surveyor_status = 'completed';
                }
            } elseif ($assignment->surveyor_status !== 'completed' && $assignment->auditor_status !== 'completed') {
                // Change surveyor_status to 'losted' and auditor_status to 'bypass'
                $assignment->auditor_status = 'bypass';
                $assignment->surveyor_status = 'losted';
            }

            $assignment->save();
        }
        */

    }

    // Start the task by distributing to each party
    public static function distributingAssignments($surveyId)
    {
        $today = Carbon::now()->startOfDay();

        $survey = Survey::findOrFail($surveyId);

        $distributedData = $survey->distributed_data ? json_decode($survey->distributed_data, true) : null;

        // Populate/repopulate = depends on are or not completed indivisual user tasks
        if($distributedData && $distributedData['surveyor']){
            // Get the most recent date of assignment for the specific survey and remove
            SurveyAssignments::removeDistributingAssignments($surveyId);

            foreach ($distributedData['surveyor'] as $value) {

                // Check if this surveyor_id has recent completed task
                $findRecentlySurveyorAssignment = DB::connection('smAppTemplate')
                    ->table('survey_assignments')
                    ->where('survey_id', $surveyId)
                    ->where('surveyor_id', intval($value['user_id']))
                    ->where('company_id', intval($value['company_id']))
                    ->whereIn('surveyor_status', ['completed'])
                    ->max(DB::raw('DATE(created_at)'));

                // If user dont have completed task, populate
                if(!$findRecentlySurveyorAssignment || $findRecentlySurveyorAssignment < $today){
                    $data = [
                        'survey_id' => intval($surveyId),
                        'surveyor_id' => intval($value['user_id']),
                        'company_id' => intval($value['company_id']),
                    ];

                    try {
                        $assignment = new SurveyAssignments;
                        $assignment->fill($data);
                        $assignment->save();
                    } catch (\Exception $e) {
                        \Log::error("Error in distributingAssignments: " . $e->getMessage());
                    }
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

        $filterCreatedAt = request('created_at', '');
        $filterCompanies = request('companies', []);

        $createdAtRange = [];
        if (!empty($filterCreatedAt)) {
            $dateRange = explode(' até ', $filterCreatedAt);

            if (count($dateRange) === 2) {
                // Date range provided
                $startDate = Carbon::createFromFormat('d/m/Y', trim($dateRange[0]))->startOfDay()->format('Y-m-d H:i:s');
                $endDate = Carbon::createFromFormat('d/m/Y', trim($dateRange[1]))->endOfDay()->format('Y-m-d H:i:s');
            } else {
                // Single date provided
                $startDate = Carbon::createFromFormat('d/m/Y', trim($filterCreatedAt))->startOfDay()->format('Y-m-d H:i:s');
                $endDate = Carbon::createFromFormat('d/m/Y', trim($filterCreatedAt))->endOfDay()->format('Y-m-d H:i:s');
            }
            $createdAtRange = [$startDate, $endDate];
        }


        // First, find on the distributedData because the survey can be not started yet
        $distributedData = $survey->distributed_data ?? null;
        if ($distributedData) {
            $decodedData = json_decode($distributedData, true);

            $surveyorData = $decodedData['surveyor'] ?? [];
            $auditorData = $decodedData['auditor'] ?? [];
        }

        // Second, get from assignments
        $surveyorQuery = SurveyAssignments::where('survey_id', $surveyId)
            ->select('surveyor_id AS user_id','company_id')
            ->when(!empty($filterCompanies), function ($query) use ($filterCompanies) {
                $query->whereIn('company_id', $filterCompanies);
            })
            ->when(!empty($createdAtRange), function ($query) use ($createdAtRange) {
                $query->whereBetween('created_at', $createdAtRange);
            })
            ->get()
            ->toArray();

        $auditorQuery = SurveyAssignments::where('survey_id', $surveyId)
            ->select('auditor_id AS user_id','company_id')
            ->when(!empty($filterCompanies), function ($query) use ($filterCompanies) {
                $query->whereIn('company_id', $filterCompanies);
            })
            ->when(!empty($createdAtRange), function ($query) use ($createdAtRange) {
                $query->whereBetween('created_at', $createdAtRange);
            })
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
                'reverse' => 'Retomar',
                'description' => 'Tarefas sendo executadas',
                'icon' => 'ri-todo-fill',
                'color' => 'info'
            ],
            'auditing' => [
                'label' => 'Em Auditoria',
                'reverse' => 'Abrir Formulário',
                'description' => 'Tarefas sendo auditadas',
                'icon' => 'ri-todo-line',
                'color' => 'secondary'
            ],
            'completed' => [
                'label' => 'Concluída',
                'reverse' => '',
                'description' => 'Tarefas que foram concluídas',
                'icon' => 'ri-check-double-fill',
                'color' => 'success'
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
                return $assignmentCreatedAt;
                break;
            case 'weekly':
                return $assignmentCreatedAt->addWeek();
                break;
            case 'biweekly':
                return $assignmentCreatedAt->addWeeks(2);
                break;
            case 'monthly':
                return $assignmentCreatedAt->addMonthNoOverflow();
                break;
            case 'annual':
                return $assignmentCreatedAt->addYear();
                break;
            default:
                return null;
                break;
        }
    }

    public static function countSurveyAssignmentSurveyorTasks($userId, $keys = false)
    {
        $keys = is_array($keys) ? $keys : ['new', 'pending', 'in_progress', 'auditing', 'completed', 'losted'];

        return SurveyAssignments::where('surveyor_id', $userId)
            ->whereIn('surveyor_status', $keys)
            ->count();
    }

    public static function countSurveyAssignmentAuditorTasks($userId, $keys = false)
    {
        $keys = is_array($keys) ? $keys : ['new', 'pending', 'in_progress', 'completed', 'losted'];

        return SurveyAssignments::where('auditor_id', $userId)
            ->whereIn('auditor_status', $keys)
            ->count();
    }




}
