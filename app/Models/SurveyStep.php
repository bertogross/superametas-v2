<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyStep extends Model
{
    use HasFactory;

    protected $connection = 'smAppTemplate';

    public $timestamps = true;

    protected $fillable = [
        'survey_id',
        'step_name',
        'step_order'
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }


}
