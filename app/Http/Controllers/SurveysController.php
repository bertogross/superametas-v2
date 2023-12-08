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

        $createdAt = $request->input('created_at');
        $status = $request->input('status');
        //$delegated_to = $request->input('delegated_to');
        //$audited_by = $request->input('audited_by');

        /**
         * START TEMPLATES QUERY
         */

        $queryTemplates = SurveyTemplates::query();
        //$queryTemplates->where('user_id', $currentUserId);
        $queryTemplates->where('condition_of','publish');

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
        //$query = $query->where('user_id', $currentUserId);
        $query = $query->where('condition_of', 'publish');

        if ($createdAt) {
            $dateRange = explode(' até ', $createdAt);
            if (is_array($dateRange) && count($dateRange) === 2) {
                $startDate = Carbon::createFromFormat('d/m/Y', trim($dateRange[0]))->startOfDay()->format('Y-m-d H:i:s');
                $endDate = Carbon::createFromFormat('d/m/Y', trim($dateRange[1]))->endOfDay()->format('Y-m-d H:i:s');

                $query->whereBetween('created_at', [$startDate, $endDate]);
            } else {
                $date = Carbon::createFromFormat('d/m/Y', $createdAt)->format('Y-m-d');

                $query->whereDate('created_at', '=', $date);
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
        $firstDate = $dateRange['first_date'] ?? null;
        $lastDate = $dateRange['last_date'] ?? null;

        // usefull if crontab is losted
        $this->populateRecurringSurveys();

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

        $surveyId = $data->id;

        $analyticCompaniesData = Survey::fetchAndTransformSurveyDataByCompanies($surveyId);

        $analyticTermsData = Survey::fetchAndTransformSurveyDataByTerms($surveyId);

        $users = getUsers();
        $userAvatars = [];
        foreach ($users as $user) {
            $userAvatars[$user->id] = [
                'name' => $user->name,
                'avatar' => getUserData($user->id)['avatar'],
            ];
        }

        $getCompanies = getActiveCompanies();
        $companies = [];
        foreach ($getCompanies as $company) {
            $companies[$company->id] = [
                'name' => $company->company_alias,
            ];
        }

        /*$stepsQuery = DB::connection('smAppTemplate')
            ->table('survey_steps')
            ->where('survey_id', $surveyId)
            ->get()
            ->toArray();
        $steps = $stepsQuery ?? null;*/

        /**
         * START get terms
         */
        $terms = [];
        $departmentsQuery = DB::connection('smAppTemplate')
            ->table('wlsm_departments')
            ->get()
            ->toArray();
        foreach ($departmentsQuery as $department) {
            $terms[$department->department_id] = [
                'name' => strtoupper($department->department_alias),
            ];
        }
        $termsQuery = DB::connection('smAppTemplate')
            ->table('survey_terms')
            ->get()
            ->toArray();
        foreach ($termsQuery as $term) {
            $terms[$term->id] = [
                'name' => strtoupper($term->name),
            ];
        }
        /**
         * END get terms
         */

        $dateRange = SurveyAssignments::getAssignmentDateRange();
        $firstDate = $dateRange['first_date'] ?? null;
        $lastDate = $dateRange['last_date'] ?? null;

        return view('surveys.show', compact(
            'data',
            'analyticCompaniesData',
            'analyticTermsData',
            'companies',
            //'steps',
            'terms',
            'userAvatars',
            'firstDate',
            'lastDate',
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
        //$queryTemplates->where('user_id', $currentUserId);

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
        //$queryTemplates->where('user_id', $currentUserId);
        $templates = $queryTemplates->orderBy('title')->paginate(50);

        $countResponses = Survey::countSurveyAllResponsesFromToday($id);

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

        $templateId = $data->template_id;

        if($currentStatus == 'new'){
            SurveyStep::populateSurveySteps($templateId, $surveyId);
        }

        if ($currentUserId != $data->user_id) {
            return response()->json(['success' => false, 'message' => 'Você não possui autorização para Inicializar/Interromper a rotina registrada por outra pessoa']);
        }

        $countResponses = Survey::countSurveyAllResponsesFromToday($surveyId);

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
        $currentUserId = auth()->id();

        // Cache::flush();
        $messages = [
            'title.required' => 'Necessário Inserir um Título',
            'template_id.required' => 'Necessário selecionar um modelo',
            'distributed_data.required' => 'Necessário delegar usuários para Vistoria e Auditoria',
            'recurring.in' => 'Escolha a recorrência',
            //'started_at' => 'Defina a data de início',
            //'ended_at' => 'Defina a data final'
        ];

        try {
            $validatedData = Validator::make($request->all(), [
                'title' => 'required|string|max:191',
                //'recurring' => 'required|in:once,daily,weekly,biweekly,monthly,annual',
                //'description' => 'nullable|string|max:1000',
                'template_id' => 'required',
                //'delegated_to' => 'required',
                //'audited_by' => 'required',
                'distributed_data' => 'required',
                'recurring' => 'required|in:daily,weekly,biweekly,monthly,annual',//once,
                //'started_at' => 'nullable',
                //'ended_at' => 'nullable',
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

        /*
        $jsonString = $request->input('distributed_data');
        $distributedData = $jsonString ? json_decode($jsonString, true) : null;
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['success' => false, 'message' => 'Invalid JSON format']);
        }
        //$validatedData['distributed_data'] = $distributedData ?? null;
        */

        $jsonString = $request->input('distributed_data');
        $distributedData = json_decode($jsonString);

        if (json_last_error() !== JSON_ERROR_NONE || !is_object($distributedData)) {
            return response()->json(['success' => false, 'message' => 'Invalid JSON format']);
        }

        $distributedData = $validatedData['distributed_data'];

        $templateId = $validatedData['template_id'];
        $templateQuery = SurveyTemplates::findOrFail($templateId);
        $templateData = $templateQuery->template_data ?? '';

        /*
        $jsonString = $request->input('delegated_to');
        $distributedData = $jsonString ? json_decode($jsonString, true) : null;
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['success' => false, 'message' => 'Invalid JSON format']);
        }
        $validatedData['delegated_to'] = $distributedData ?? null;


        $jsonString = $request->input('audited_by');
        $distributedData = $jsonString ? json_decode($jsonString, true) : null;
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['success' => false, 'message' => 'Invalid JSON format']);
        }
        $validatedData['audited_by'] = $distributedData ?? null;
        */

        $validatedData['user_id'] = $currentUserId;

        if ($id) {

            // Update operation
            $survey = Survey::findOrFail($id);

            $surveyId = $survey->id;

            $surveyStatus = $survey->status;

            $startedAt = $survey->started_at;
            $endedAt = $survey->ended_at;

            $countResponses = Survey::countSurveyAllResponses($surveyId);

            if( $startedAt && $countResponses > 0 ){
                $validatedData['started_at'] = Carbon::createFromFormat('d/m/Y', trim($startedAt))->startOfDay()->format('Y-m-d H:i:s');
            }

            $validatedData['ended_at'] = !$endedAt ? Carbon::createFromFormat('d/m/Y', trim($endedAt))->endOfDay()->format('Y-m-d H:i:s') : '';

            if($surveyStatus == 'new'){
                $validatedData['template_data'] = $templateData;
            }

            // Check if the current user is the creator
            if ($currentUserId != $survey->user_id) {
                return response()->json(['success' => false, 'message' => 'Você não possui autorização para editar um registro gerado por outra pessoa']);
            }

            $survey->update($validatedData);

            // Update from model if task is not started
            if($surveyStatus == 'new'){
                SurveyStep::populateSurveySteps($templateId, $surveyId);
            }

            $countResponses = Survey::countSurveyAllResponsesFromToday($surveyId);

            $distributedData = $survey->distributed_data ?? null;

            // Start the task by distributing to each party
            SurveyAssignments::distributingAssignments($surveyId, $distributedData);

            return response()->json([
                'success' => true,
                'message' => 'Dados atualizados!',
                'id' => $surveyId,
                'json' => $distributedData
            ]);
        } else {
            // Store operation
            $validatedData['template_data'] = $templateData;

            $survey = new Survey;
            $survey->fill($validatedData);
            $survey->save();

            $surveyId = $survey->id;

            SurveyStep::populateSurveySteps($templateId, $surveyId);

            return response()->json([
                'success' => true,
                'message' => 'Dados adicionados com sucesso!',
                'id' => $surveyId,
                'json' => $distributedData
            ]);
        }
    }

    // A crontab to start recurring tasks
    public function populateRecurringSurveys($database = null)
    {
        // Set the database connection name dynamically
        if ($database) {
            $databaseName = 'smApp' . $database;
            config(['database.connections.smAppTemplate.database' => $databaseName]);
        }

        try {
            $surveys = Survey::where('status', 'started')
                            ->where('condition_of', 'publish')
                            ->orderBy('created_at')
                            ->paginate(50);

            if ($surveys->count() > 0) {
                foreach ($surveys as $survey) {
                    Survey::checkSurveyAssignmentUntilYesterday($survey->id);
                    Survey::startNewAssignmentIfSurveyIsRecurring($survey->id);
                }
            }
        } catch (\Exception $e) {
            // Handle the exception or log it
            Log::error('Error in loadRecurringSurveys: ' . $e->getMessage());
        }
    }



}
