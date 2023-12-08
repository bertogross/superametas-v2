<?php

namespace App\Http\Controllers;

use App\Models\SurveyTopic;
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
            //'compliance_survey.required' => 'Marque: Conforme ou Não Conforme',
            //'compliance_survey.in' => 'Marque Apenas se Conforme ou Não Conforme',//'O campo compliance_survey deve ser yes, no ou na.',
        ];

        try {
            $validatedData = Validator::make($request->all(), [
                'company_id' => 'required',
                'survey_id' => 'required',
                'step_id' => 'required',
                'topic_id' => 'required',
                //'compliance_survey' => 'required|in:yes,no,na',
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

        $assignmentId = $request->input('assignment_id');

        $surveyorAssignmentData = SurveyAssignments::findOrFail($assignmentId) ?? null;

        // Get authenticated user ID
        $currentUserId = auth()->id();

        if ($currentUserId != $surveyorAssignmentData->surveyor_id) {
            return response()->json(['success' => false, 'message' => 'Você não possui autorização para prosseguir com a tarefa delegada a outra pessoa']);
        }

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
                'message' => 'Esta Vistoria foi perdida pois o prazo expirou e por isso não poderá mais ser editada',
            ]);
        }

        $complianceSurvey = $request->input('compliance_survey');
        if(!$complianceSurvey){
            exit;
        }

        $attachmentIds = $request->input('attachment_ids');

        /*if( $complianceSurvey == 'no' && !$attachmentIds ){
            return response()->json([
                'success' => false,
                'message' => 'Necessário enviar ao menos uma foto apontando o motivo da Não Conformidade',
                'action' => 'changeToPending'
            ]);
        }*/
        if( !$attachmentIds ){
            return response()->json([
                'success' => false,
                'message' => 'Necessário enviar ao menos uma foto',
                'action' => 'changeToPending'
            ]);
        }
        $attachmentIdsInt = $attachmentIds ? array_map('intval', $attachmentIds) : [];

        $comment = $request->input('comment_survey');
        $comment = trim($comment);

        if( $complianceSurvey == 'no' && empty($comment) ){
            return response()->json([
                'success' => false,
                'message' => 'Necessário descrever o motivo da Não Conformidade',
                'action' => 'changeToPending'
            ]);
        }

        // Prepare data for saving
        $data = $request->only(['assignment_id', 'company_id', 'survey_id', 'step_id', 'topic_id', 'compliance_survey', 'comment_survey']);

        $data['surveyor_id'] = $currentUserId;

        $data['assignment_id'] = $assignmentId;

        // Check if attachmentIdsInt is empty and handle accordingly
        $data['attachments_survey'] = !empty($attachmentIdsInt) ? json_encode($attachmentIdsInt) : json_encode([]);

        // Prevent error from JavaScript if input[name="response_id"] was cracked.
        // Check if exists the response. If exist get the Id and update.
        $existingResponse = SurveyResponse::where('survey_id', $data['survey_id'])
            ->where('assignment_id', $data['assignment_id'])
            ->where('step_id', $data['step_id'])
            ->where('topic_id', $data['topic_id'])
            ->where('company_id', $data['company_id'])
            ->first();
        if($existingResponse){
            $id = $existingResponse->id;
        }

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

        $countTopics = SurveyTopic::countSurveyTopics($data['survey_id']);

        // Count the number of steps that have been finished
        $countResponses = SurveyResponse::countSurveySurveyorResponses($currentUserId, $data['survey_id'], $data['company_id'], $assignmentId);

        // Return success response
        return response()->json([
            'success' => true,
            'message' => $id ? 'Os dados deste tópico foram atualizados' : 'Os dados deste tópico foram salvos',
            'id' => $SurveyResponse->id,
            'countResponses' => $countResponses,
            'countTopics' => $countTopics,
            'showFinalizeButton' => $countResponses < $countTopics ? false : true
        ]);
    }

    public function responsesAuditorStoreOrUpdate(Request $request, $id = null)
    {
        $messages = [
            'company_id.required' => 'O campo company_id é obrigatório',
            'survey_id.required' => 'O campo survey_id é obrigatório',
            'step_id.required' => 'O campo step_id é obrigatório',
            'topic_id.required' => 'O campo topic_id é obrigatório',
            //'compliance_audit.required' => 'Marque: Conforme ou Não Conforme',
            //'compliance_audit.in' => 'Marque Apenas se Conforme ou Não Conforme',//'O campo compliance_audit deve ser yes, no ou na.',
        ];

        try {
            $validatedData = Validator::make($request->all(), [
                'company_id' => 'required',
                'survey_id' => 'required',
                'step_id' => 'required',
                'topic_id' => 'required',
                //'compliance_audit' => 'required|in:yes,no,na',
                //'comment_audit' => 'sometimes|string',
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

        $assignmentId = $request->input('assignment_id');

        $auditorAssignmentData = SurveyAssignments::findOrFail($assignmentId) ?? null;

        // Get authenticated user ID
        $currentUserId = auth()->id();

        if ($currentUserId != $auditorAssignmentData->auditor_id) {
            return response()->json(['success' => false, 'message' => 'Você não possui autorização para prosseguir com a tarefa delegada a outra pessoa']);
        }

        $aditorStatus = $auditorAssignmentData->auditor_status;
        if( $aditorStatus == 'completed' ){
            return response()->json([
                'success' => false,
                'message' => 'Esta Auditoria já foi finalizada e não poderá ser editada',
            ]);
        }
        if($aditorStatus == 'losted' ){
            return response()->json([
                'success' => false,
                'message' => 'Esta Auditoria foi perdida pois o prazo expirou e por isso não poderá mais ser editada',
            ]);
        }

        $complianceAudit = $request->input('compliance_audit');
        if(!$complianceAudit){
            exit;
        }

        $attachmentIds = $request->input('attachment_ids');

        if( $complianceAudit == 'no' && !$attachmentIds ){
            return response()->json([
                'success' => false,
                'message' => 'Necessário enviar ao menos uma foto comprovando o motivo de haver Indeferido',
                'action' => 'changeToPending'
            ]);
        }

        $comment = $request->input('comment_audit');
        $comment = trim($comment);

        if( $complianceAudit == 'no' && empty($comment) ){
            return response()->json([
                'success' => false,
                'message' => 'Necessário descrever o motivo de haver Indeferido',
                'action' => 'changeToPending'
            ]);
        }

        $attachmentIdsInt = $attachmentIds ? array_map('intval', $attachmentIds) : [];

        // Prepare data for saving
        $data = $request->only(['assignment_id', 'company_id', 'survey_id', 'step_id', 'topic_id', 'compliance_audit', 'comment_audit']);

        $data['auditor_id'] = $currentUserId;

        $data['assignment_id'] = $assignmentId;

        // Check if attachmentIdsInt is empty and handle accordingly
        $data['attachments_audit'] = !empty($attachmentIdsInt) ? json_encode($attachmentIdsInt) : json_encode([]);

        // Prevent error from JavaScript if input[name="response_id"] was cracked.
        // Check if exists the response. If exist get the Id and update.
        $existingResponse = SurveyResponse::where('survey_id', $data['survey_id'])
            ->where('assignment_id', $data['assignment_id'])
            ->where('step_id', $data['step_id'])
            ->where('topic_id', $data['topic_id'])
            ->where('company_id', $data['company_id'])
            ->first();
        if($existingResponse){
            $id = $existingResponse->id;
        }

        if ($id) {
            // Update existing survey response
            $SurveyResponse = SurveyResponse::findOrFail($id);
            $SurveyResponse->update($data);
        } else {
            // Create new survey response
            $SurveyResponse = SurveyResponse::create($data);
        }

        // Change SurveyorAssignment status
        SurveyAssignments::changeAuditorAssignmentStatus($assignmentId, 'in_progress');

        $countTopics = SurveyTopic::countSurveyTopics($data['survey_id']);

        // Count the number of steps that have been finished
        $countResponses = SurveyResponse::countSurveyAuditorResponses($currentUserId, $data['survey_id'], $data['company_id'], $assignmentId);

        // Return success response
        return response()->json([
            'success' => true,
            'message' => $id ? 'Os dados deste tópico foram atualizados' : 'Os dados deste tópico foram salvos',
            'id' => $SurveyResponse->id,
            'countResponses' => $countResponses,
            'countTopics' => $countTopics,
            'showFinalizeButton' => $countResponses < $countTopics ? false : true
        ]);
    }

}
