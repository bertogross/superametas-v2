<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SurveyAssignments extends Model
{
    use HasFactory;

    protected $connection = 'smAppTemplate';

    public $timestamps = true;

    protected $fillable = ['survey_id', 'company_id', 'surveyor_id', 'auditor_id', 'surveyor_status', 'auditor_status'];

    // Start the task by distributing to each party
    public static function distributingAssignments($surveyId, $distributedData)
    {
        $distributedData = $distributedData ? json_decode($distributedData, true) : null;
        $distributedDataMerged = [];
        foreach ($distributedData['audited_by'] as $audited) {
            foreach ($distributedData['delegated_to'] as $delegated) {
                if ($audited['company_id'] === $delegated['company_id']) {
                    $distributedDataMerged[] = [
                        'company_id' => $audited['company_id'],
                        'auditor_id' => $audited['user_id'],
                        'surveyor_id' => $delegated['user_id']
                    ];
                    break;
                }
            }
        }

        // Delete all data where created_at is equal to today
        $today = now()->startOfDay(); // Get the start of today
        SurveyAssignments::whereDate('created_at', $today)->where('survey_id', $surveyId)->delete();

        foreach ($distributedDataMerged as $value) {
            $data = [
                'surveyor_id' => intval($value['surveyor_id']),
                'auditor_id' => intval($value['auditor_id']),
                'survey_id' => intval($surveyId),
                'company_id' => intval($value['company_id']),
            ];

            try {
                $assignment = new SurveyAssignments;
                $assignment->fill($data);
                $assignment->save();
            } catch (\Exception $e) {
                // TODO
                // Handle the exception or log it
            }
        }

    }

    public static function changeSurveyorAssignmentStatus($assignmentId, $status)
    {
        $data = SurveyAssignments::findOrFail($assignmentId);

        $surveyorId = $data->surveyor_id;
        $surveyId = $data->survey_id;
        $companyId = $data->company_id;

        if($status == 'pending'){
            // Field survey status column
            DB::connection('smAppTemplate')->table('surveys')
                ->where('id', $surveyId)
                ->update([
                    'status' => 'started',
                ]);
        }elseif($status == 'auditing'){
            // When Surveyor finish the task, transfer to Auditor make revision

            // new status
            $column['auditor_status'] = 'new';
            $data->update($column);
        }

        // new status
        $column['surveyor_status'] = $status;
        $data->update($column);
    }


}
