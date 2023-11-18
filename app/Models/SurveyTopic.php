<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyTopic extends Model
{
    use HasFactory;

    protected $connection = 'smAppTemplate';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'survey_id',
        'step_id',
        'question',
        'topic_order'
    ];

    public function step()
    {
        return $this->belongsTo(SurveyStep::class);
    }

    public static function populateSurveyTopics($topics, $stepId, $surveyId){
        $userId = auth()->id();

        if( $topics && $stepId && $surveyId){
            
            foreach($topics as $topicIndex => $topic){
                $question = $topic['question'] ?? '';
                $originalPosition = $topic['original_position'] ?? $topicIndex;
                $newPosition = $topic['new_position'] ?? $originalPosition;

                if($question){
                    $fill['user_id'] = $userId;
                    $fill['survey_id'] = $surveyId;
                    $fill['step_id'] = $stepId;
                    $fill['question'] = $question;
                    $fill['topic_order'] = intval($newPosition);

                    $SurveyTopic = new SurveyTopic;
                    $SurveyTopic->fill($fill);
                    $SurveyTopic->save();
                }
            }
        }

    }


}
