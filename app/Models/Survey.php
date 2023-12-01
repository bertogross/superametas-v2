<?php

namespace App\Models;

use Carbon\Carbon;
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
        'template_id',
        'user_id',
        'status',
        'old_status',
        'distributed_data',
        'template_data',
        'recurring',
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

    public static function getSurveyStatusTranslations() {
        return [
            'waiting' => [
                'label' => 'Aguardando',
                'reverse' => '',
                'description' => 'Aguardando finalização da Vistoria',
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
                'description' => 'Tarefa registrada mas não inicializada',
                'icon' => 'ri-play-fill',
                'color' => 'primary'
            ],
            'stopped' => [
                'label' => 'Parado',
                'reverse' => 'Reiniciar',
                'description' => 'Tarefa interrompida',
                'icon' => 'ri-stop-mini-fill',
                'color' => 'danger'
            ],
            'pending' => [
                'label' => 'Pendente',
                'reverse' => 'Abrir Formulário',
                'description' => 'Tarefa que foi inicializada mas ainda não possui dados de progresso',
                'icon' => 'ri-survey-line',
                'color' => 'warning'
            ],
            'in_progress' => [
                'label' => 'Em Progresso',
                'reverse' => 'Abrir Formulário',
                'description' => 'Tarefa que está em andamento',
                'icon' => 'ri-time-line',
                'color' => 'info'
            ],
            'auditing' => [
                'label' => 'Em Auditoria',
                'reverse' => 'Abrir Formulário',
                'description' => 'Tarefa que está sendo revisada/auditada',
                'icon' => 'ri-todo-line',
                'color' => 'secondary'
            ],
            'completed' => [
                'label' => 'Concluída',
                'reverse' => '',
                'description' => 'Tarefa que foi concluída',
                'icon' => 'ri-check-double-fill',
                'color' => 'success'
            ],
            'losted' => [
                'label' => 'Perdida',
                'reverse' => '',
                'description' => 'Tarefa não concluída no prazo',
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
            'once' => [
                'label' => 'Uma vez',
                'description' => 'Tarefa ou processo que será executado uma vez',
                'color' => 'primary'
            ],
            'daily' => [
                'label' => 'Diária',
                'description' => 'Tarefa ou processo que será repetido diáriamente',
                'color' => 'secondary'
            ],
            /*
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
            */
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
            if ($assignment->surveyor_status === 'auditing' && $assignment->auditor_status !== 'completed') {
                // Change auditor_status to 'losted' and surveyor_status to 'completed'
                $assignment->auditor_status = 'losted';
                $assignment->surveyor_status = 'completed';
            } elseif ($assignment->surveyor_status !== 'auditing') {
                // Change surveyor_status to 'losted' and auditor_status to 'losted'
                $assignment->surveyor_status = 'losted';
                $assignment->auditor_status = 'losted';
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
                        // Check if today is the specific day of the week for weekly recurrence
                        // Example: if ($today->isMonday()) { ... }
                        break;
                    case 'biweekly':
                        // Check if today is the 1st or 15th of the month for biweekly recurrence
                        break;
                    case 'monthly':
                        // Check if today matches the specific day of the month for monthly recurrence
                        break;
                    case 'annual':
                        // Check if today matches the specific day and month for annual recurrence
                        break;
                }
            }
        }
    }


}
