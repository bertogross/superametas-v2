<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    use HasFactory;

    protected $connection = 'smAppTemplate';

    public $timestamps = true;

    protected $fillable = [
        'company_id',
        'surveyor_id',
        'auditor_id',
        'step_id',
        'topic_id',
        'survey_id',
        'compliance_survey',
        'compliance_audit',
        'comment_survey',
        'comment_audit',
        'attachments_survey',
        'attachments_audit'
    ];


}
