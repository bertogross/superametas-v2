<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Audit extends Model
{
    use HasFactory;

    protected $connection = 'smAppTemplate';

    public $timestamps = true;

    protected $fillable = [
        'created_by',
        'current_user_editor',
        'delegated_to',
        'description',
        'assigned_to',
        'audited_by',
        'status',
        //'priority',
        'due_date',
        'completed_at',
        'audited_at'
    ];

    public function meta()
    {
        return $this->hasMany(AuditMeta::class);
    }

    public static function countByStatus($status = false) {
        $query = DB::connection('smAppTemplate')
            ->table('audits')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status');

        if($status){
            $query->where('status', $status);
        }

        $results = $query->get();

        $statusCounts = [];
        foreach ($results as $result) {
            $statusCounts[$result->status] = $result->total;
        }

        return $statusCounts;
    }


    public static function getAuditStatusTranslations() {
        return [
            'pending' => [
                'label' => 'Pendente',
                'description' => 'Tarefa ou processo que ainda não foi iniciado.',
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
                'description' => 'Tarefa ou processo que está sendo revisado ou auditado.',
                'icon' => 'ri-todo-line',
                'color' => 'success'
            ],
            'completed' => [
                'label' => 'Finalizada',
                'description' => 'Tarefa ou processo que foi concluído com sucesso.',
                'icon' => 'ri-check-double-fill',
                'color' => 'secondary'
            ]
        ];
    }


}
