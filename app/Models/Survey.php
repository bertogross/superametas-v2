<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Survey extends Model
{
    use HasFactory;

    protected $connection = 'smAppTemplate';
    //$model->setConnection('smAppTemplate');

    public $timestamps = true;

    protected $fillable = [
        'title',
        'assigned_to',
        'delegated_to',
        'audited_by',
        //'status',
        //'priority',
        'description',
        'jsondata',
        'start_date',
        'completed_at',
        'audited_at'
    ];

    public function metas()
    {
        return $this->hasMany(SurveyMeta::class);
    }

    public static function countByStatus($status = false)
    {
        $query = DB::connection('smAppTemplate')
            ->table('surveys')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status');

        if($status){
            $query->where('status', $status);
        }

        //$user = auth()->id();
       //$query = $query->where('user_id', $user->id);

        $results = $query->get();

        $statusCounts = [];
        foreach ($results as $result) {
            $statusCounts[$result->status] = $result->total;
        }

        return $statusCounts;
    }


    public static function getSurveyStatusTranslations() {
        return [
            'new' => [
                'label' => 'Novo',
                'description' => 'Tarefa ou processo registrado mas não inicializado.',
                'icon' => 'ri-flag-2-line',
                'color' => 'primary'
            ],
            'stoped' => [
                'label' => 'Parado',
                'description' => 'Tarefa ou processo Parado.',
                'icon' => 'ri-stop-circle-line',
                'color' => 'danger'
            ],
            'pending' => [
                'label' => 'Pendente',
                'description' => 'Tarefa ou processo que foi inicializado mas ainda não possui dados de progresso.',
                'icon' => 'ri-time-line',
                'color' => 'warning'
            ],
            'in_progress' => [
                'label' => 'Em Progresso',
                'description' => 'Tarefa ou processo que está em andamento.',
                'icon' => 'ri-run-line',
                'color' => 'info'
            ],
            'audited' => [
                'label' => 'Em Auditoria',
                'description' => 'Tarefa ou processo que está sendo revisado/auditado.',
                'icon' => 'ri-todo-line',
                'color' => 'secondary'
            ],
            'completed' => [
                'label' => 'Finalizada',
                'description' => 'Tarefa ou processo que foi concluído.',
                'icon' => 'ri-check-double-fill',
                'color' => 'success'
            ]
        ];
    }

    /**
     * Get all survey composes by type.
     */
    public static function getByType($data, $type = 'custom')
    {
        //$user = auth()->id();

        /*
        $query = self::where('type', $type);
       // $query = $query->where('user_id', $user->id);

        if ($status) {
            $query = $query->where('status', $status);
        }
        */

        //return $query->get();
    }

    public static function reorderingData($data)
    {
        $transformedData = [];

        if($data){
            // First, sort the steps according to their new_position
            foreach ($data as $step) {
                $newPosition = $step['stepData']['new_position'] ?? 0;
                $transformedData[$newPosition] = $step;
            }
            ksort($transformedData); // Sort by key to maintain the order of steps

            // Now, sort the topicData for each step
            foreach ($transformedData as $stepPosition => &$step) {
                $sortedTopicData = [];

                if( isset($step['topicData']) ){
                    foreach ($step['topicData'] as $topic) {
                        $newTopicPosition = $topic['new_position'] ?? 0;
                        $sortedTopicData[$newTopicPosition] = $topic;
                    }
                    ksort($sortedTopicData); // Sort by key to maintain the order of topics

                    $step['topicData'] = array_values($sortedTopicData); // Re-index the array
                }
            }
            unset($step); // Break the reference to the last element
        }
        return $transformedData;
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

}
