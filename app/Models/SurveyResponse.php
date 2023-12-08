<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'assignment_id',
        'compliance_survey',
        'compliance_audit',
        'comment_survey',
        'comment_audit',
        'attachments_survey',
        'attachments_audit'
    ];

    // Count the number of responses from surveyor
    public static function countSurveySurveyorResponses($surveyorId, $surveyId, $companyId, $assignmentId) {
        //$today = Carbon::today();

        return SurveyResponse::where('survey_id', $surveyId)
            ->where('surveyor_id', $surveyorId)
            //->where('company_id', $companyId)
            ->where('assignment_id', $assignmentId)
            ->whereNotNull('compliance_survey')
            ->whereNotNull('attachments_survey')
            //->whereDate('created_at', '=', $today)
            ->count();

    }

    // Count the number of responses from auditor
    public static function countSurveyAuditorResponses($auditorId, $surveyId, $companyId, $assignmentId) {
        $today = Carbon::today();

        return SurveyResponse::where('survey_id', $surveyId)
            ->where('auditor_id', $auditorId)
            //->where('company_id', $companyId)
            ->where('assignment_id', $assignmentId)
            ->whereNotNull('compliance_audit')
            //->whereDate('created_at', '=', $today)
            ->count();
    }

}
