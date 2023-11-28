<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
//use App\Models\SurveyCompose;
use App\Models\Survey;
use App\Models\SurveyStep;
//use App\Models\SurveyMeta;
use App\Models\SurveyTerms;
use App\Models\SurveyTopic;
use Illuminate\Http\Request;
use App\Models\SurveyResponse;
use App\Models\SurveyTemplates;
use App\Models\SurveyAssignments;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SurveysController extends Controller
{
    public function index(Request $request)
    {

        $currentUserId = auth()->id();

        $created_at = $request->input('created_at');
        $status = $request->input('status');
        //$delegated_to = $request->input('delegated_to');
        //$audited_by = $request->input('audited_by');

        /**
         * START TEMPLATES QUERY
         */

        //$queryTemplates = SurveyTemplates::findOrFail($id);
        //$queryTemplates = SurveyTemplates::where('user_id', $currentUserId)->first();
        //$queryTemplates = SurveyTemplates::where('user_id', $currentUserId)->get();
        $queryTemplates = SurveyTemplates::query();
        $queryTemplates->where('user_id', $currentUserId);
        $queryTemplates->where('condition_of','publish');

        /*
        if ($created_at) {
            $dates = explode(' até ', $created_at);
            if (is_array($dates) && count($dates) === 2) {
                $start_date = Carbon::createFromFormat('d/m/Y', $dates[0])->format('Y-m-d');
                $end_date = Carbon::createFromFormat('d/m/Y', $dates[1])->format('Y-m-d') . ' 23:59:59';
                $queryTemplates->whereBetween('created_at', [$start_date, $end_date]);
            } else {
                $start_date = Carbon::createFromFormat('d/m/Y', $created_at)->format('Y-m-d');
                $queryTemplates->whereDate('created_at', '=', $start_date);
            }
        }
        */
        $templates = $queryTemplates->orderBy('created_at')->paginate(10);
        /**
         * END TEMPLATES QUERY
         */

        /**
         * START SURVEYS QUERY
         */
        $query = Survey::query();
        if ($status) {
            $query->where('status', $status);
        }
        $query = $query->where('user_id', $currentUserId);
        $query = $query->where('condition_of', 'publish');

        if ($created_at) {
            $dates = explode(' até ', $created_at);
            if (is_array($dates) && count($dates) === 2) {
                $start_date = Carbon::createFromFormat('d/m/Y', $dates[0])->format('Y-m-d');
                $end_date = Carbon::createFromFormat('d/m/Y', $dates[1])->format('Y-m-d') . ' 23:59:59';
                $query->whereBetween('created_at', [$start_date, $end_date]);
            } else {
                $start_date = Carbon::createFromFormat('d/m/Y', $created_at)->format('Y-m-d');
                $query->whereDate('created_at', '=', $start_date);
            }
        }

        $data = $query->orderBy('created_at')->paginate(10);
        /**
         * END SURVEYS QUERY
         */

        $getSurveyRecurringTranslations = Survey::getSurveyRecurringTranslations();

        $getSurveyStatusTranslations = Survey::getSurveyStatusTranslations();

        $getAuthorizedCompanies = getAuthorizedCompanies();

        $dateRange = Survey::getSurveysDateRange();
        $firstDate = $dateRange['first_date'];
        $lastDate = $dateRange['last_date'];

        return view('surveys.index', compact(
            'data',
            'templates',
            'getSurveyRecurringTranslations',
            'getSurveyStatusTranslations',
            'getAuthorizedCompanies',
            'firstDate',
            'lastDate',
        ));
    }

    public function show(Request $request, $id = null)
    {
        if (!$id) {
            abort(404);
        }

        $data = Survey::findOrFail($id);

        $getSurveyRecurringTranslations = Survey::getSurveyRecurringTranslations();

        return view('surveys.show', compact(
            'data',
            'getSurveyRecurringTranslations'
        ) );
    }

    // Add
    public function create()
    {
        // Cache::flush();

        session()->forget('success');

        $currentUserId = auth()->id();

        $data = null;

        $users = getUsers();

        $getAuthorizedCompanies = getAuthorizedCompanies();

        $getSurveyRecurringTranslations = Survey::getSurveyRecurringTranslations();

        $queryTemplates = SurveyTemplates::query();
        $queryTemplates->where('user_id', $currentUserId);

        $templates = $queryTemplates->orderBy('title')->paginate(50);

        $countResponses = 0;

        return view('surveys.create', compact(
                'data',
                'templates',
                'users',
                'getAuthorizedCompanies',
                'getSurveyRecurringTranslations',
                'countResponses'
            )
        );
    }

    public function edit(Request $request, $id = null)
    {
        // Cache::flush();

        session()->forget('success');

        $currentUserId = auth()->id();

        if (!$id) {
            abort(404);
        }

        $data = Survey::findOrFail($id);
        /*
        $data = Survey::query();
        $data->where('id', $id);
        $data->where('user_id', $currentUserId);
        */

        // Check if the current user is the creator
        if ($currentUserId != $data->user_id) {
            return response()->json(['success' => false, 'message' => 'Você não possui autorização para editar um registro gerado por outra pessoa']);
        }

        $users = getUsers();

        $getAuthorizedCompanies = getAuthorizedCompanies();

        $getSurveyRecurringTranslations = Survey::getSurveyRecurringTranslations();

        $queryTemplates = SurveyTemplates::query();
        $queryTemplates->where('user_id', $currentUserId);
        $templates = $queryTemplates->orderBy('title')->paginate(50);

        $countResponses = countSurveyAllResponsesFromToday($id);

        return view('surveys.edit', compact(
                'data',
                'templates',
                'users',
                'getAuthorizedCompanies',
                'getSurveyRecurringTranslations',
                'countResponses'
            )
        );
    }

    //start/stop survey
    public function changeStatus(Request $request)
    {
        $currentUserId = auth()->id();

        $surveyId = $request->input('id');
        $surveyId = intval($surveyId);

        $data = Survey::findOrFail($surveyId);

        $currentStatus = $data->status;

        if ($currentUserId != $data->user_id) {
            return response()->json(['success' => false, 'message' => 'Você não possui autorização para Inicializar/Interromper a rotina registrada por outra pessoa']);
        }

        $countResponses = countSurveyAllResponsesFromToday($surveyId);

        if($countResponses > 0){
            return response()->json(['success' => false, 'message' => 'Não será possível interromper esta tarefa pois dados já estão sendo coletados.']);
        }

        $distributedData = $data->distributed_data ?? null;

        //$oldStatus = $data->old_status ?? $currentStatus;

        //$message = 'Status da tarefa foi atualizado com sucesso';
        //$newStatus = $oldStatus;

        switch ($currentStatus) {
            case 'new':
                $newStatus = 'started';

                $message = 'Vistoria inicializada com sucesso';

                // Start the task by distributing to each party
                SurveyAssignments::distributingAssignments($surveyId, $distributedData);
                break;
            case 'stopped':
                $newStatus = 'started';

                $message = 'Vistoria reinicializada com sucesso';

                // Restart the task by distributing to each party
                SurveyAssignments::distributingAssignments($surveyId, $distributedData);
                break;
            case 'started':
                $newStatus = 'stopped';

                $message = 'Vistoria interrompida';
                break;
            default:
               echo "i is not equal to 0, 1 or 2";
        }

        $columns['status'] = $newStatus;
        //$columns['old_status'] = $currentStatus;

        // Save the changes
        $data->update($columns);

        $message = $newStatus == 'stopped' ? 'Vistoria foi interrompida' : $message;

        // Return a success response
        return response()->json(['success' => true, 'message' => $message]);
    }

    public function storeOrUpdate(Request $request, $id = null)
    {
        // Cache::flush();
        $messages = [
            'template_id.required' => 'Necessário selecionar um modelo',
            'distributed_data.required' => 'Necessário delegar usuários para Vistoria e Auditoria',
            'recurring.in' => 'Escolha a recorrência',
        ];

        try {
            $validatedData = Validator::make($request->all(), [
                //'title' => 'required|string|max:191',
                //'recurring' => 'required|in:once,daily,weekly,biweekly,monthly,annual',
                //'description' => 'nullable|string|max:1000',
                'template_id' => 'required',
                //'delegated_to' => 'required',
                //'audited_by' => 'required',
                'distributed_data' => 'required',
                'recurring' => 'required|in:once,daily,weekly,biweekly,monthly,annual',
                //'current_user_editor' => 'nullable',
                //'assigned_to' => 'nullable',
                //'status' => 'required|in:new,stopped,trash,pending,in_progress,completed,auditing',
                //'survey_compose_custom_id' => 'nullable',
                //'survey_compose_default_id' => 'nullable',
                //'custom_fields' => 'nullable|array',
                //'custom_fields.*.type' => 'required_with:custom_fields|string|max:20',
                //'custom_fields.*.name' => 'required_with:custom_fields|string|max:30',
                //'custom_fields.*.label' => 'required_with:custom_fields|string|max:50',
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

        // Convert array inputs to JSON strings for storage
        $validatedData = array_map(function ($value) {
            return is_array($value) ? json_encode($value) : $value;
        }, $validatedData);

        $jsonString = $request->input('distributed_data');
        $distributed_data = $jsonString ? json_decode($jsonString, true) : null;
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['success' => false, 'message' => 'Invalid JSON format']);
        }
        //$validatedData['distributed_data'] = $distributed_data ?? null;

        $distributed_data = $validatedData['distributed_data'];

        $templateId = $validatedData['template_id'];
        $templateQuery = SurveyTemplates::findOrFail($templateId);
        $templateData = $templateQuery->template_data ?? '';

        /*
        $jsonString = $request->input('delegated_to');
        $distributed_data = $jsonString ? json_decode($jsonString, true) : null;
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['success' => false, 'message' => 'Invalid JSON format']);
        }
        $validatedData['delegated_to'] = $distributed_data ?? null;


        $jsonString = $request->input('audited_by');
        $distributed_data = $jsonString ? json_decode($jsonString, true) : null;
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['success' => false, 'message' => 'Invalid JSON format']);
        }
        $validatedData['audited_by'] = $distributed_data ?? null;
        */

        $currentUserId = auth()->id();
        $validatedData['user_id'] = $currentUserId;

        /*
        $recurring = $request->input('recurring');
        $validatedData['recurring'] = !empty($recurring) && in_array($recurring, ['once','daily','weekly','biweekly','monthly','annual']) ? $recurring : 'once';
        */

        if ($id) {
            // Update operation
            $survey = Survey::findOrFail($id);

            if($survey->status == 'new'){
                $validatedData['template_data'] = $templateData;
            }

            // Check if the current user is the creator
            if ($currentUserId != $survey->user_id) {
                return response()->json(['success' => false, 'message' => 'Você não possui autorização para editar um registro gerado por outra pessoa']);
            }

            $survey->update($validatedData);

            $surveyId = $survey->id;

            // Update from model if task is not started
            if($survey->status == 'new'){
                SurveyStep::populateSurveySteps($validatedData['template_id'], $surveyId);
            }

            return response()->json([
                'success' => true,
                'message' => 'Dados atualizados!',
                'id' => $surveyId,
                'json' => $distributed_data
            ]);
        } else {
            // Store operation
            $validatedData['template_data'] = $templateData;

            $survey = new Survey;
            $survey->fill($validatedData);
            $survey->save();

            $surveyId = $survey->id;

            SurveyStep::populateSurveySteps($validatedData['template_id'], $surveyId);

            return response()->json([
                'success' => true,
                'message' => 'Dados adicionados com sucesso!',
                'id' => $surveyId,
                'json' => $distributed_data
            ]);
        }
    }



}
