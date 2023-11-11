<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GoalSales extends Model
{
    use HasFactory;

    protected $connection = 'smAppTemplate';
    //$model->setConnection('smAppTemplate');

    public $timestamps = true;

    public static function getSaleDateRange()
    {
        $firstDate = DB::connection('smAppTemplate')->table('wlsm_sales')
            ->select(DB::raw('DATE_FORMAT(MIN(date_sale), "%Y-%m") as first_date'))
            ->first();

        $lastDate = DB::connection('smAppTemplate')->table('wlsm_sales')
            ->select(DB::raw('DATE_FORMAT(MAX(date_sale), "%Y-%m") as last_date'))
            ->first();

        return [
            'first_date' => $firstDate->first_date ?? date('Y-m'),
            'last_date' => $lastDate->last_date ?? date('Y-m'),
        ];
    }


}
