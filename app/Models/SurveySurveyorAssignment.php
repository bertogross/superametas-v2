<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SurveySurveyorAssignment extends Pivot
{
    use HasFactory;

    protected $connection = 'smAppTemplate';

    public $timestamps = true;

    public $incrementing = false;
    protected $table = 'survey_auditor_assignments';

    // Define relationships to Survey, User, and Company models
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
