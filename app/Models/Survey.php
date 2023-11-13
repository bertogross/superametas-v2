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

    public $timestamps = true;

    protected $fillable = [
        'template_id',
        'user_id',
        'status',
        'priority',
        'start_date',
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
