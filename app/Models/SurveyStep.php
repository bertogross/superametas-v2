<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SurveyTemplates;
use App\Models\SurveyTopic;

class SurveyStep extends Model
{
    use HasFactory;

    protected $connection = 'smAppTemplate';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'survey_id',
        'term_id',
        'step_order'
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function topics()
    {
        return $this->hasMany(SurveyTopic::class, 'step_id');
    }

    public static function populateSurveySteps($templateId, $surveyId){
        $currentUserId = auth()->id();

        // Delete existing survey steps for the given surveyId
        SurveyStep::where('survey_id', $surveyId)->delete();

        // Delete existing survey topics for the given surveyId
        SurveyTopic::where('survey_id', $surveyId)->delete();

        $data = SurveyTemplates::findOrFail($templateId);

        $decodedData = isset($data->template_data) && is_string($data->template_data) ? json_decode($data->template_data, true) : $data->template_data;

        $reorderingData = SurveyTemplates::reorderingData($decodedData);

        $result = $reorderingData ?? null;

        foreach($result as $stepIndex => $step){
            $stepData = $step['stepData'] ?? null;
            //$stepName = $stepData['step_name'] ?? '';
            $termId = $stepData['term_id'] ?? '';
            //$type = $stepData['type'] ?? 'custom';
            $originalPosition = $stepData['original_position'] ?? $stepIndex;
            $newPosition = $stepData['new_position'] ?? $originalPosition;

            $topics = $step['topics'] ?? null;

            $fill['user_id'] = $currentUserId;
            $fill['survey_id'] = intval($surveyId);
            $fill['term_id'] = intval($termId);
            $fill['step_order'] = intval($newPosition);

            $SurveyStep = new SurveyStep;
            $SurveyStep->fill($fill);
            $SurveyStep->save();

            $stepId = $SurveyStep->id;

            SurveyTopic::populateSurveyTopics($topics, $stepId, $surveyId);

        }

    }


}
