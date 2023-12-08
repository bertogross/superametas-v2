<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\SurveyResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Survey extends Model
{
    use HasFactory;

    protected $connection = 'smAppTemplate';

    public $timestamps = true;

    protected $fillable = [
        'title',
        'template_id',
        'user_id',
        'status',
        'old_status',
        'distributed_data',
        'template_data',
        'recurring',
        'started_at',
        'ended_at',
        'priority',
        'completed_at',
        'audited_at'
    ];

    // Define relationships here
    public function steps()
    {
        return $this->hasMany(SurveyStep::class);
    }

    /*
    public function metas()
    {
        return $this->hasMany(SurveyMeta::class);
    }
    */

    public static function countByStatus($status = false)
    {

        $currentUserId = auth()->id();

        $query = DB::connection('smAppTemplate')
            ->table('surveys')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status');

        if($status){
            $query->where('status', $status);
        }

        $query = $query->where('user_id', $currentUserId);

        $results = $query->get();

        $statusCounts = [];
        foreach ($results as $result) {
            $statusCounts[$result->status] = $result->total;
        }

        return $statusCounts;
    }

    public static function countSurveyAllResponses($surveyId)
    {
        $today = Carbon::today();

        return SurveyResponse::where('survey_id', $surveyId)
            //->whereDate('created_at', '=', $today)
            ->count();
    }

    public static function countSurveyAllResponsesFromToday($surveyId)
    {
        $today = Carbon::today();

        return SurveyResponse::where('survey_id', $surveyId)
            ->whereDate('created_at', '=', $today)
            ->count();
    }

    public static function getSurveyStatusTranslations()
    {
        return [
            'waiting' => [
                'label' => 'Aguardando',
                'reverse' => '',
                'description' => 'Aguardando a finalização da primeira etapa, Vistoria',
                'icon' => 'ri-pause-mini-line',
                'color' => 'primary'
            ],
            'started' => [
                'label' => 'Ativa',
                'reverse' => 'Interromper',
                'description' => 'Tarefa Inicializada',
                'icon' => 'ri-pause-mini-line',
                'color' => 'success'
            ],
            'new' => [
                'label' => 'Nova',
                'reverse' => 'Iniciar',
                'description' => 'Tarefas registradas mas não inicializadas',
                'icon' => 'ri-play-fill',
                'color' => 'primary'
            ],
            'stopped' => [
                'label' => 'Parado',
                'reverse' => 'Reiniciar',
                'description' => 'Tarefas interrompidas',
                'icon' => 'ri-stop-mini-fill',
                'color' => 'danger'
            ],
            'pending' => [
                'label' => 'Pendente',
                'reverse' => 'Abrir Formulário',
                'description' => 'Tarefa que foram inicializadas mas ainda não possuem dados de progresso',
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
                'description' => 'Tarefas que estão sendo revisadas/auditadas',
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
            ]
        ];
    }

    public static function getSurveysDateRange()
    {
        $firstDate = DB::connection('smAppTemplate')->table('surveys')
            ->select(DB::raw('DATE_FORMAT(MIN(created_at), "%Y-%m-%d") as first_date'))
            ->first();

        $lastDate = DB::connection('smAppTemplate')->table('surveys')
            ->select(DB::raw('DATE_FORMAT(MAX(created_at), "%Y-%m-%d") as last_date'))
            ->first();

        return [
            'first_date' => $firstDate->first_date ?? date('Y-m-d'),
            'last_date' => $lastDate->last_date ?? date('Y-m-d'),
        ];
    }

    public static function getSurveyRecurringTranslations()
    {
        return [
            'daily' => [
                'label' => 'Diária',
                'description' => 'Tarefa ou processo que será repetido diáriamente',
                'color' => 'secondary'
            ],
            'weekly' => [
                'label' => 'Semanal',
                'description' => 'Tarefa ou processo que será repetido semanalmente',
            ],
            'biweekly' => [
                'label' => 'Quinzenal',
                'description' => 'Tarefa ou processo que será repetido quinzenalmente',
            ],
            'monthly' => [
                'label' => 'Mensal',
                'description' => 'Tarefa ou processo que será repetido uma vez por mês',
            ],
            'annual' => [
                'label' => 'Anual',
                'description' => 'Tarefa ou processo que será repetido uma vez ao ano',
            ]
        ];
    }

    // Check the 'survey_assignments' table to see which tasks were not completed by yesterday and change the status to 'losted'
    public static function checkSurveyAssignmentUntilYesterday($surveyId)
    {
        $yesterday = Carbon::yesterday();

        // Get all survey assignments that were not completed by yesterday
        $assignments = SurveyAssignments::where('survey_id', $surveyId)
            ->whereDate('created_at', '<=', $yesterday)
            ->get();

        foreach ($assignments as $assignment) {
            if ( ( $assignment->surveyor_status === 'completed' || $assignment->surveyor_status === 'auditing' ) && $assignment->auditor_status !== 'completed' ) {
                // Change auditor_status to 'bypass' and surveyor_status to 'completed'
                $assignment->auditor_status = 'bypass';
                $assignment->surveyor_status = 'completed';
            } /*elseif ( $assignment->surveyor_status === 'auditing' && $assignment->auditor_status !== 'completed' ) {
                // Change auditor_status to 'losted' and surveyor_status to 'completed'
                $assignment->auditor_status = 'losted';
                $assignment->surveyor_status = 'completed';
            }*/ elseif ( $assignment->surveyor_status !== 'auditing' && $assignment->surveyor_status !== 'completed' ) {
                // Change surveyor_status to 'losted' and auditor_status to 'losted'
                $assignment->auditor_status = 'bypass';
                $assignment->surveyor_status = 'losted';
            }

            $assignment->save();
        }
    }

    public static function startNewAssignmentIfSurveyIsRecurring($surveyId)
    {
        $today = Carbon::today();
        $survey = Survey::findOrFail($surveyId);

        $status = $survey->status;

        if($status == 'started'){
            $recurring = $survey->recurring;
            $distributedData = $survey->distributed_data ?? null;

            // Check if there are survey assignments for today
            $assignmentsCount = SurveyAssignments::where('survey_id', $surveyId)
                ->whereDate('created_at', '=', $today)
                ->count();

            // If there are no assignments for today, check the recurrence pattern
            if ($assignmentsCount == 0) {
                switch ($recurring) {
                    case 'daily':
                        SurveyAssignments::distributingAssignments($surveyId, $distributedData);
                        break;
                    case 'weekly':
                        // TODO
                        // Check if today is the specific day of the week for weekly recurrence
                        // Example: if ($today->isMonday()) { ... }
                        break;
                    case 'biweekly':
                        // TODO
                        // Check if today is the 1st or 15th of the month for biweekly recurrence
                        break;
                    case 'monthly':
                        // TODO
                        // Check if today matches the specific day of the month for monthly recurrence
                        break;
                    case 'annual':
                        // TODO
                        // Check if today matches the specific day and month for annual recurrence
                        break;
                }
            }
        }
    }

    /*
    public static function fetchAndTransformSurveyDataByCompanies($surveyId)
    {
        $filterCreatedAt = request('created_at', '');
        $createdAtRange = [];

        if (!empty($filterCreatedAt)) {
            $dateRange = explode(' até ', $filterCreatedAt);

            if (count($dateRange) === 2) {
                // Date range provided
                $startDate = Carbon::createFromFormat('d/m/Y', $dateRange[0])->format('Y-m-d');
                $endDate = Carbon::createFromFormat('d/m/Y', $dateRange[1])->format('Y-m-d') . ' 23:59:59';

            } else {
                // Single date provided
                $startDate = Carbon::createFromFormat('d/m/Y', $filterCreatedAt)->format('Y-m-d');
                $endDate = Carbon::createFromFormat('d/m/Y', $filterCreatedAt)->format('Y-m-d') . ' 23:59:59';
            }
            $createdAtRange = [$startDate, $endDate];
        }

        $filterCompanies = request('companies', []);

        $analyticsData = SurveyAssignments::where('survey_assignments.survey_id', $surveyId)
            ->join('survey_responses', 'survey_assignments.id', '=', 'survey_responses.assignment_id')
            ->select(
                //'survey_assignments.*', // You might want to select specific fields here
                'survey_assignments.survey_id',
                'survey_assignments.company_id',
                'survey_assignments.surveyor_id',
                'survey_assignments.auditor_id',
                'survey_assignments.surveyor_status',
                'survey_assignments.auditor_status',
                'survey_assignments.created_at',
                'survey_responses.compliance_survey',
                //'survey_responses.compliance_audit'
                // Add other fields you need here
            )
            ->where('survey_assignments.surveyor_status', 'completed')
            ->where('survey_responses.compliance_survey', '!=', null)
            ->when(!empty($filterCompanies), function ($query) use ($filterCompanies) {
                return $query->whereIn('survey_assignments.company_id', $filterCompanies);
            })
            ->when(!empty($createdAtRange), function ($query) use ($createdAtRange) {
                $query->whereBetween('survey_assignments.created_at', $createdAtRange);
            })
            ->get()
            ->toArray();

        $transformedArray = [];

        foreach ($analyticsData as $item) {
            $dateKey = Carbon::parse($item['created_at'])->format('d-m-Y');
            $companyId = $item['company_id'];

            $transformedArray[$dateKey][$companyId][] = $item;
        }

        return $transformedArray;
    }

    public static function fetchAndTransformSurveyDataByTerms($surveyId, $assignmentId = null)
    {
        $filterCreatedAt = request('created_at', '');
        $createdAtRange = [];

        if (!empty($filterCreatedAt)) {
            $dateRange = explode(' até ', $filterCreatedAt);

            if (count($dateRange) === 2) {
                // Date range provided
                $startDate = Carbon::createFromFormat('d/m/Y', $dateRange[0])->format('Y-m-d');
                $endDate = Carbon::createFromFormat('d/m/Y', $dateRange[1])->format('Y-m-d') . ' 23:59:59';

            } else {
                // Single date provided
                $startDate = Carbon::createFromFormat('d/m/Y', $filterCreatedAt)->format('Y-m-d');
                $endDate = Carbon::createFromFormat('d/m/Y', $filterCreatedAt)->format('Y-m-d') . ' 23:59:59';
            }
            $createdAtRange = [$startDate, $endDate];
        }

        $filterCompanies = request('companies', []);

        $analyticsData = SurveyAssignments::where('survey_assignments.survey_id', $surveyId)
            ->join('survey_responses', 'survey_assignments.id', '=', 'survey_responses.assignment_id')
            ->join('survey_steps', 'survey_responses.step_id', '=', 'survey_steps.id')
            ->select(
                //'survey_assignments.*', // You might want to select specific fields here
                'survey_assignments.survey_id',
                'survey_assignments.company_id',
                'survey_assignments.surveyor_id',
                'survey_assignments.auditor_id',
                'survey_assignments.surveyor_status',
                'survey_assignments.auditor_status',
                'survey_assignments.created_at',
                'survey_responses.compliance_survey',
                'survey_steps.term_id'
                // Add other fields you need here
            )
            ->where('survey_assignments.surveyor_status', 'completed')
            ->where('survey_responses.compliance_survey', '!=', null)
            ->when($assignmentId, function ($query) use ($assignmentId) {
                $query->where('survey_assignments.id', $assignmentId);
            })
            ->when(!empty($filterCompanies), function ($query) use ($filterCompanies) {
                $query->whereIn('survey_assignments.company_id', $filterCompanies);
            })
            ->when(!empty($createdAtRange), function ($query) use ($createdAtRange) {
                $query->whereBetween('survey_assignments.created_at', $createdAtRange);
            })
            ->get()
            ->toArray();

        $transformedArray = [];

        foreach ($analyticsData as $item) {
            $dateKey = Carbon::parse($item['created_at'])->format('d-m-Y');
            $termId = $item['term_id'];

            $transformedArray[$dateKey][$termId][] = $item;
        }

        return $transformedArray;
    }
    */

    public static function fetchAndTransformSurveyDataByCompanies($surveyId)
    {
        $filterCreatedAt = request('created_at', '');
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


        $filterCompanies = request('companies', []);

        $analyticsData = SurveyAssignments::where('survey_assignments.survey_id', $surveyId)
            ->join('survey_responses', 'survey_assignments.id', '=', 'survey_responses.assignment_id')
            ->select(
                //'survey_assignments.*', // You might want to select specific fields here
                'survey_assignments.survey_id',
                'survey_assignments.company_id',
                'survey_assignments.surveyor_id',
                'survey_assignments.auditor_id',
                'survey_assignments.surveyor_status',
                'survey_assignments.auditor_status',
                'survey_assignments.created_at',
                'survey_responses.compliance_survey',
                //'survey_responses.compliance_audit'
                // Add other fields you need here
            )
            ->where('survey_assignments.surveyor_status', 'completed')
            ->where('survey_responses.compliance_survey', '!=', null)
            ->when(!empty($filterCompanies), function ($query) use ($filterCompanies) {
                return $query->whereIn('survey_assignments.company_id', $filterCompanies);
            })
            ->when(!empty($createdAtRange), function ($query) use ($createdAtRange) {
                $query->whereBetween('survey_assignments.created_at', $createdAtRange);
            })
            ->get()
            ->toArray();

        $transformedArray = [];

        foreach ($analyticsData as $item) {
            $dateKey = Carbon::parse($item['created_at'])->format('d-m-Y');
            $companyId = $item['company_id'];

            $transformedArray[$dateKey][$companyId][] = $item;
        }

        return $transformedArray;
    }


    public static function fetchAndTransformSurveyDataByTerms($surveyId, $assignmentId = null)
    {
        $filterCreatedAt = request('created_at', '');
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


        $filterCompanies = request('companies', []);

        $analyticsData = SurveyAssignments::where('survey_assignments.survey_id', $surveyId)
            ->join('survey_responses', 'survey_assignments.id', '=', 'survey_responses.assignment_id')
            ->join('survey_steps', 'survey_responses.step_id', '=', 'survey_steps.id')
            ->select(
                //'survey_assignments.*', // You might want to select specific fields here
                'survey_assignments.survey_id',
                'survey_assignments.company_id',
                'survey_assignments.surveyor_id',
                'survey_assignments.auditor_id',
                'survey_assignments.surveyor_status',
                'survey_assignments.auditor_status',
                'survey_assignments.created_at',
                'survey_responses.compliance_survey',
                'survey_steps.term_id'
            )
            ->where('survey_assignments.surveyor_status', 'completed')
            ->where('survey_responses.compliance_survey', '!=', null)
            ->when($assignmentId, function ($query) use ($assignmentId) {
                $query->where('survey_assignments.id', $assignmentId);
            })
            ->when(!empty($filterCompanies), function ($query) use ($filterCompanies) {
                $query->whereIn('survey_assignments.company_id', $filterCompanies);
            })
            ->when(!empty($createdAtRange), function ($query) use ($createdAtRange) {
                $query->whereBetween('survey_assignments.created_at', $createdAtRange);
            })
            ->get()
            ->toArray();

        $transformedArray = [];

        foreach ($analyticsData as $item) {
            $dateKey = Carbon::parse($item['created_at'])->format('d-m-Y');
            $termId = $item['term_id'];

            $transformedArray[$dateKey][$termId][] = $item;
        }

        return $transformedArray;
    }


}
