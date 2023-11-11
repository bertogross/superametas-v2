<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SurveyCompose extends Model
{
    use HasFactory;

    // Specifies the database connection for this model.
    protected $connection = 'smAppTemplate';

    protected $fillable = [
        'user_id',
        'title',
        'jsondata',
        //'status',
    ];                                                                                                         

    protected $casts = [
        'jsondata' => 'array',
    ];

    /**
     * Get all survey composes by type.
     */
    public static function getAllByType($type = 'custom', $status = null)
    {
        $user = Auth::user();

        $query = self::where('type', $type);
        $query = $query->where('user_id', $user->id);

        if ($status) {
            $query = $query->where('status', $status);
        }

        return $query->get();
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

}
