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
        ];

        try {
            $validatedData = Validator::make($request->all(), [
                'company_id' => 'required',
                'survey_id' => 'required',
                'step_id' => 'required',
                'topic_id' => 'required',
                'compliance_survey' => 'required|in:yes,no,na',
                //'comment_survey' => 'sometimes|string',
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

        $attachmentIds = $request->input('attachment_ids');
        if(!$attachmentIds){
            return response()->json([
                'success' => false,
                'message' => 'Necessário enviar ao menos uma foto'
            ]);
        }
        $attachmentIdsInt = array_map('intval', $attachmentIds);

        $assignmentId = $request->input('assignment_id');

        $surveyorAssignmentData = SurveyAssignments::findOrFail($assignmentId) ?? null;

        $surveyorStatus = $surveyorAssignmentData->surveyor_status;
        if( $surveyorStatus == 'auditing' ){
            return response()->json([
                'success' => false,
                'message' => 'Esta Vistoria já foi enviada para Auditoria e não poderá ser editada',
            ]);
        }
        if($surveyorStatus == 'losted' ){
            return response()->json([
                'success' => false,
                'message' => 'Esta Vistoria foi perdida pois o prezo expirou e por isso não poderá mais ser editada',
            ]);
        }

        // Prepare data for saving
        $data = $request->only(['company_id', 'survey_id', 'step_id', 'topic_id', 'compliance_survey', 'comment_survey']);

        $data['surveyor_id'] = $currentUserId;

        $data['attachments_survey'] = $attachmentIdsInt ? json_encode($attachmentIdsInt) : '';

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
            'message' => $id ? 'Os dados deste tópico foram atualizados' : 'Os dados deste tópico foram salvos',
            'id' => $SurveyResponse->id,
            'count' => $countResponses,
        ]);
    }



}
