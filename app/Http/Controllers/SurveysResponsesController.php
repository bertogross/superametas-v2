<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SurveyResponse;
use App\Models\SurveyAssignments;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SurveysResponsesController extends Controller
{
    public function responsesSurveyorStoreOrUpdate(Request $request, $id = null)
    {
        $messages = [
            'company_id.required' => 'O campo company_id é obrigatório',
            'survey_id.required' => 'O campo survey_id é obrigatório',
            'step_id.required' => 'O campo step_id é obrigatório',
            'topic_id.required' => 'O campo topic_id é obrigatório',
            'compliance_survey.required' => 'Marque: Conforme ou Não Conforme',
            'compliance_survey.in' => 'Marque Apenas se Conforme ou Não Conforme',//'O campo compliance_survey deve ser yes, no ou na.',
            //'attachment_id_survey' => 'Envie uma foto'
        ];

        try {
            $validatedData = Validator::make($request->all(), [
                'company_id' => 'required',
                'survey_id' => 'required',
                'step_id' => 'required',
                'topic_id' => 'required',
                'compliance_survey' => 'required|in:yes,no,na',
                //'comment_survey' => 'sometimes|string',
                //'attachment_id_survey' => 'required'
            ], $messages)->validate();
        } catch (ValidationException $e) {
            $errors = $e->errors();

            $errorMessages = '';
            foreach ($errors as $field => $messages) {
                foreach ($messages as $message) {
                    $errorMessages = $message;
                    break;
                }
            }

            return response()->json([
                'success' => false,
                'message' => $errorMessages
            ]);
        }

        // Get authenticated user ID
        $currentUserId = auth()->id();

        $assignmentId = $request->input('assignment_id');

        // Prepare data for saving
        $data = $request->only(['company_id', 'survey_id', 'step_id', 'topic_id', 'compliance_survey', 'comment_survey', 'attachment_id_survey']);
        $data['surveyor_id'] = $currentUserId;

        if ($id) {
            // Update existing survey response
            $SurveyResponse = SurveyResponse::findOrFail($id);
            $SurveyResponse->update($data);
        } else {
            // Create new survey response
            $SurveyResponse = SurveyResponse::create($data);
        }

        // Change SurveyorAssignment status
        SurveyAssignments::changeSurveyorAssignmentStatus($assignmentId, 'in_progress');

        // Count the number of steps that have been finished
        $countResponses = countSurveySurveyorResponses($currentUserId, $data['survey_id'], $data['company_id']);

        // Return success response
        return response()->json([
            'success' => true,
            'message' => $id ? 'Dados foram atualizados!' : 'Dados foram salvos!',
            'id' => $SurveyResponse->id,
            'count' => $countResponses,
        ]);
    }



}
