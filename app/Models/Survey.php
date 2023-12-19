<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\SurveyResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Survey extends Model
{
    use HasFactory;

    protected $connection = 'smAppTemplate';

    public $timestamps = true;

    protected $casts = [
        'start_at' => 'datetime',
        'end_in' => 'datetime',
    ];

    protected $fillable = [
        'title',
        'template_id',
        'companies',
        'user_id',
        'status',
        'old_status',
        'distributed_data',
        'template_data',
        'recurring',
        'start_at',
        'end_in',
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
             'new' => [
                'label' => 'Novo',
                'reverse' => 'Iniciar',
                'description' => 'Rotina registrada mas não inicializada',
                'icon' => 'ri-play-fill',
                'color' => 'primary'
            ],
            'scheduled' => [
                'label' => 'Agendado',
                'reverse' => '',
                'description' => 'Rotina agendada',
                'icon' => 'ri-calendar-2-line',
                'color' => 'info'
            ],
            'started' => [
                'label' => 'Ativo',
                'reverse' => 'Interromper',
                'description' => 'Rotina inicializada',
                'icon' => 'ri-pause-mini-line',
                'color' => 'success'
            ],
            'stopped' => [
                'label' => 'Interrompido',
                'reverse' => 'Reiniciar',
                'description' => 'Rotina interrompida',
                'icon' => 'ri-restart-line',
                'color' => 'danger'
            ],
            'completed' => [
                'label' => 'Concluído',
                'reverse' => '',
                'description' => 'Rotina concluída',
                'icon' => 'ri-check-double-fill',
                'color' => 'success'
            ],
           'filed' => [
                'label' => 'Arquivado',
                'reverse' => '',
                'description' => 'Rotina arquivada',
                'icon' => 'ri-skull-line',
                'color' => 'warning'
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
            ],
            'daily' => [
                'label' => 'Diária',
                'description' => 'Tarefa ou processo que será repetido diáriamente',
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
                'bg-color' => 'dark'
            ]
        ];
    }

    public static function extractUserIds($analyticTermsData) {
        $userIds = [];

        foreach ($analyticTermsData as $termData) {
            foreach ($termData as $dateData) {
                foreach ($dateData as $data) {
                    if (!empty($data['surveyor_id']) && !isset($userIds[$data['surveyor_id']])) {
                        $getUserData = getUserData($data['surveyor_id']);

                        $userIds[$data['surveyor_id']] = [
                            'name' => $getUserData['name'],
                            'avatar' => $getUserData['avatar']
                        ];
                    }
                    if (!empty($data['auditor_id']) && !isset($userIds[$data['auditor_id']])) {
                        $getUserData = getUserData($data['auditor_id']);

                        $userIds[$data['auditor_id']] =  [
                            'name' => $getUserData['name'],
                            'avatar' => $getUserData['avatar']
                        ];
                    }
                }
            }
        }

        return $userIds;
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

    // Populate assignments
    public static function startSurveyAssignments($surveyId)
    {
        $today = Carbon::today();
        $survey = Survey::findOrFail($surveyId);

        $startAt = $survey->start_at; // Date when the survey started
        $endIn = $survey->end_in; // The final date

        $status = $survey->status;
        $recurring = $survey->recurring;

        if ($status == 'started') {

            $distributedData = $survey->distributed_data ?? null;

            // Check if there are survey assignments for today
            $assignmentsCount = SurveyAssignments::where('survey_id', $surveyId)
                ->whereDate('created_at', '=', $today)
                ->count();

            // If there are no assignments for today, check the recurrence pattern
            if ($assignmentsCount == 0) {
                switch ($recurring) {
                    case 'once':
                        SurveyAssignments::distributingAssignments($surveyId, $distributedData);
                        break;
                    case 'daily':
                        SurveyAssignments::distributingAssignments($surveyId, $distributedData);
                        break;
                    case 'weekly':
                        // Calculate the day of the week for both $startAt and $today
                        $specificDayOfWeek = $startAt->dayOfWeek;
                        $currentDayOfWeek = $today->dayOfWeek;

                        if ($specificDayOfWeek === $currentDayOfWeek) {
                            SurveyAssignments::distributingAssignments($surveyId, $distributedData);
                        }
                        break;
                    case 'biweekly':
                        // Calculate 15 days after $startAt for biweekly recurrence
                        $biweeklyDate = $startAt->addDays(15);

                        // Check if today matches the calculated biweekly date
                        if ($today->equalTo($biweeklyDate)) {
                            SurveyAssignments::distributingAssignments($surveyId, $distributedData);
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
                            SurveyAssignments::distributingAssignments($surveyId, $distributedData);
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
                            SurveyAssignments::distributingAssignments($surveyId, $distributedData);
                        }
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

            //$transformedArray[$dateKey][$termId][] = $item;
            $transformedArray[$termId][$dateKey][] = $item;
        }

        return $transformedArray;
    }


    // START Used with crontab to start recurring tasks
    public static function populateSurveys($database = null)
    {

        self::setDatabaseConnection($database);

        self::processSurveysWithStatus('scheduled', function ($survey) {
            $today = Carbon::now()->startOfDay();

            if ( $survey->start_at && $survey->start_at == $today ) {
                $survey->update(['status' => 'started']);

                Survey::checkSurveyAssignmentUntilYesterday($survey->id);
                Survey::startSurveyAssignments($survey->id);
            }
        });

        self::processSurveysWithStatus('new', function ($survey) {
            $today = Carbon::now()->startOfDay();

            if ( $survey->start_at && $survey->start_at <= $today ) {
                $survey->update(['status' => 'started']);

                Survey::checkSurveyAssignmentUntilYesterday($survey->id);
                Survey::startSurveyAssignments($survey->id);
            }
        });

        self::processSurveysWithStatus('started', function ($survey) {
            $today = Carbon::now()->startOfDay();

            if ( $survey->end_in && $today > $survey->end_in ) {
                $survey->update(['status' => 'completed']);
            } else {
                Survey::checkSurveyAssignmentUntilYesterday($survey->id);
                Survey::startSurveyAssignments($survey->id);
            }
        });
    }
    private static function setDatabaseConnection($database)
    {
        if ($database) {
            $databaseName = 'smApp' . $database;
            if (!DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$databaseName])) {
                return;
            }
            config(['database.connections.smAppTemplate.database' => $databaseName]);
        }
    }
    private static function processSurveysWithStatus($status, $callback)
    {
        try {
            $surveys = Survey::where('status', $status)->orderBy('created_at')->get();
            foreach ($surveys as $survey) {
                $callback($survey);
            }
        } catch (\Exception $e) {
            \Log::error("Error in populateSurveys with status {$status}: " . $e->getMessage());
        }
    }
    // END Used with crontab to start recurring tasks



}
