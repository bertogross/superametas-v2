<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SurveyTemplates extends Model
{
    use HasFactory;

    protected $connection = 'smAppTemplate';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'template_data',
    ];

    // Define relationships here
    public function steps()
    {
        return $this->hasMany(SurveyStep::class);
    }


    public static function reorderingData($data)
    {
        $transformedData = [];

        if($data){
            // First, sort the steps according to their new_position
            foreach ($data as $step) {
                $newPosition = $step['stepData']['new_position'] ?? 0;
                $transformedData[intval($newPosition)] = $step;
            }
            ksort($transformedData); // Sort by key to maintain the order of steps

            // Now, sort the topics for each step
            foreach ($transformedData as $stepPosition => &$step) {
                $sortedTopicData = [];

                if( isset($step['stepData']['topics']) ){
                    foreach ($step['stepData']['topics'] as $topic) {
                        $newTopicPosition = $topic['new_position'] ?? 0;
                        $sortedTopicData[intval($newTopicPosition)] = $topic;
                    }
                    ksort($sortedTopicData); // Sort by key to maintain the order of topics

                    $step['stepData']['topics'] = array_values($sortedTopicData); // Re-index the array
                }
            }
            unset($step); // Break the reference to the last element
        }
        return $transformedData;
    }


    /**
     * Get all survey composes by type.
     */
    public static function getByType($data, $type = 'custom')
    {
        if($data){
            $filteredData = array_filter($data, function ($entry) use ($type) {
                // Check if 'stepData' exists and 'type' matches the specified type
                return isset($entry['stepData']) && $entry['stepData']['type'] === $type;
            });
        }

        // Return the filtered array
        return $data && $filteredData ? array_values($filteredData) : null;
    }

    /*
    public static function getSurveyTemplateDataFromId($templateId){
        $data = SurveyTemplates::findOrFail($templateId);

        return $data->template_data ? json_decode($data->template_data, true) : null;

    }
    */

    // usefull when new default sector/department is included
    public static function mergeTemplateDataArrays($data1, $data2) {
        $mergedData = [];

        foreach ($data1 as $key => $value1) {
            $termId1 = intval($value1['stepData']['term_id']);
            $mergedData[$key] = $value1; // Copy the original data

            foreach ($data2 as $value2) {
                $termId2 = intval($value2['stepData']['term_id']);

                if ($termId2 === $termId1) {
                    // Merge stepData from data2 into the new array
                    $mergedData[$key] = array_merge($value1, $value2);
                    break;
                }
            }
        }

        return $mergedData;
    }


}
