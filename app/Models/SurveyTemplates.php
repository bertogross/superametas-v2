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
        'recurring',
        'completed_at'
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
                $transformedData[$newPosition] = $step;
            }
            ksort($transformedData); // Sort by key to maintain the order of steps

            // Now, sort the topics for each step
            foreach ($transformedData as $stepPosition => &$step) {
                $sortedTopicData = [];

                if( isset($step['stepData']['topics']) ){
                    foreach ($step['stepData']['topics'] as $topic) {
                        $newTopicPosition = $topic['new_position'] ?? 0;
                        $sortedTopicData[$newTopicPosition] = $topic;
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

    public static function getSurveyRecurringTranslations() {
        return [
            'once' => [
                'label' => 'Uma vez',
                'description' => 'Tarefa ou processo que será executado uma vez.',
            ],
            'daily' => [
                'label' => 'Diária',
                'description' => 'Tarefa ou processo que será repetido diáriamente.',
            ],
            /*
            'weekly' => [
                'label' => 'Semanal',
                'description' => 'Tarefa ou processo que será repetido semanalmente.',
            ],
            'biweekly' => [
                'label' => 'Quinzenal',
                'description' => 'Tarefa ou processo que será repetido quinzenalmente.',
            ],
            'monthly' => [
                'label' => 'Mensal',
                'description' => 'Tarefa ou processo que será repetido uma vez por mês.',
            ],
            'annual' => [
                'label' => 'Anual',
                'description' => 'Tarefa ou processo que será repetido uma vez ao ano.',
            ]
            */
        ];
    }

}
