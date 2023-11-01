<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use App\Models\Audit;
use App\Models\AuditMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AuditsController extends Controller
{
    // Specify the database connection to be used for this model
    protected $connection = 'smAppTemplate';

    public function index(Request $request)
    {
        $status = e($request->input('status'));
        $delegated_to = e($request->input('delegated_to'));
        $audited_by = e($request->input('audited_by'));

        $created_at = e($request->input('created_at'));

        $query = Audit::query();

        // Search by status
        if ($status) {
            $query->where('status', $status);
        }

        // Search by delegated_to and or audited_by
        if ($delegated_to) {
            $query->whereIn('delegated_to', $delegated_to);
        } elseif ($audited_by) {
            $query->whereIn('audited_by', $audited_by);
        } elseif ($audited_by && $delegated_to) {
            $query->whereIn('audited_by', $audited_by)->orWhereIn('delegated_to', $delegated_to);
        }

        // Search by created_at
        /*if ($created_at) {
            $dates = explode(' até ', $created_at);
            if (is_array($dates) && count($dates) === 2) {
                $start_date = $dates[0];
                $end_date = $dates[1]. ' 23:59:59';
                $query->whereBetween('created_at', [$start_date, $end_date]);
            } else {
                $query->whereDate('created_at', '=', $created_at);
            }
        }*/
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

        $audits = $query->orderBy('updated_at')->paginate(10);

        $auditStatusCount = Audit::countByStatus(); // Call the function on the Audit model

        $getAuditStatusTranslations = Audit::getAuditStatusTranslations();

        $users = getUsers();

        $usersByRole = getUsersByRole(User::ROLE_AUDIT);

        $delegated_to = request('delegated_to');
        $delegated_to = is_array($delegated_to) ? $delegated_to : array();

        $audited_by = request('audited_by');
        $audited_by = is_array($audited_by) ? $audited_by : array();

        return view('audits.index', compact(
            'audits',
            'users',
            'usersByRole',
            'auditStatusCount',
            'getAuditStatusTranslations',
            'delegated_to',
            'audited_by'
        ));
    }


    public function show($id)
    {
        $audit = Audit::findOrFail($id);

        if (!$audit) {
            abort(404);
        }

        return view('audits.single', compact('audit') );
    }


    public function update(Request $request, $id = null)
    {
        $validatedData = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,audited',
            'assigned_to' => 'nullable',
            'due_date' => 'nullable|date_format:d/m/Y',
            'delegated_to' => 'nullable',
            'audited_by' => 'nullable',
            'current_user_editor' => 'nullable',
            'description' => 'nullable|string|max:1000',
            'custom_fields' => 'nullable|array',
            'custom_fields.*.type' => 'required_with:custom_fields|string|max:20',
            'custom_fields.*.name' => 'required_with:custom_fields|string|max:30',
            'custom_fields.*.label' => 'required_with:custom_fields|string|max:50',
        ]);

        if ($id) {
            // Update operation
            $audit = Audit::findOrFail($id);

            // Check if the current user is the creator
            if (auth()->id() != $audit->created_by) {
                return redirect()->back()->with('error', 'You are not authorized to edit this audit task.');
            }

            $validatedData['due_date'] = $validatedData['due_date'] ?? null ? Carbon::createFromFormat('d/m/Y', $validatedData['due_date'])->format('Y-m-d') : null;
            $validatedData['completed_at'] = $validatedData['completed_at'] ?? null ? Carbon::createFromFormat('d/m/Y', $validatedData['completed_at'])->format('Y-m-d') : null;
            $validatedData['audited_at'] = $validatedData['audited_at'] ?? null ? Carbon::createFromFormat('d/m/Y', $validatedData['audited_at'])->format('Y-m-d') : null;

            $audit->update($validatedData);

            // Update custom fields
            if (isset($validatedData['custom_fields'])) {
                $customFields = json_encode($validatedData['custom_fields']);
                AuditMeta::updateAuditMeta($audit->id, 'custom_fields', $customFields);
            }

            //return redirect()->route('auditsShowURL', $audit)->with('success', 'Audit updated successfully');
            return response()->json(['success' => true, 'message' => 'Audit saved successfully!']);
        } else {
            // Store operation
            $audit = $this->store($request);
            return $audit;
        }
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,audited',
            'assigned_to' => 'nullable',
            'due_date' => 'nullable|date_format:d/m/Y',
            'created_by' => 'required',
            'current_user_editor' => 'required',
            'delegated_to' => 'nullable',
            'audited_by' => 'nullable',
            'description' => 'nullable|string|max:1000',
            'custom_fields' => 'nullable|array',
            'custom_fields.*.type' => 'required_with:custom_fields|string|in:text,textarea,select,radio,checkbox,file',
            'custom_fields.*.name' => 'required_with:custom_fields|string|max:30',
            'custom_fields.*.label' => 'required_with:custom_fields|string|max:50',
        ]);

        // Convert date formats
        $validatedData['due_date'] = $validatedData['due_date'] ?? null ? Carbon::createFromFormat('d/m/Y', $validatedData['due_date'])->format('Y-m-d') : null;
        $validatedData['completed_at'] = $validatedData['completed_at'] ?? null ? Carbon::createFromFormat('d/m/Y', $validatedData['completed_at'])->format('Y-m-d') : null;
        $validatedData['audited_at'] = $validatedData['audited_at'] ?? null ? Carbon::createFromFormat('d/m/Y', $validatedData['audited_at'])->format('Y-m-d') : null;

        // Create the audit
        $audit = Audit::create($validatedData);

        // Store custom fields
        if (isset($validatedData['custom_fields'])) {
            $customFields = json_encode($validatedData['custom_fields']);
            AuditMeta::updateAuditMeta($audit->id, 'custom_fields', $customFields);
        }

        // Redirect to the audit's detail page with a success message
        //return redirect()->route('auditsShowURL', $audit)->with('success', 'Audit task created successfully.');
        return response()->json(['success' => true, 'message' => 'Audit updated successfully!']);
    }



    /**
     * Get the content for the modal edit.
     *
     * @param int|null
     * @return \Illuminate\View\View
     */
    public function getAuditEditModalContent(Request $request)
    {
        $data = [];

        $auditId = request('id');

        //$audit = Audit::findOrFail($auditId);

        if($auditId){
            $data = DB::connection($this->connection)
                ->table('audits')
                ->where('id', $auditId)
                ->get()
                ->toArray();
        }

        $users = getUsers();

        $usersByRole = getUsersByRole(User::ROLE_AUDIT);

        $getCompaniesAuthorized = getCompaniesAuthorized();

        $getAuditStatusTranslations = Audit::getAuditStatusTranslations();

        $customFields = AuditMeta::getAuditMeta($auditId, 'custom_fields');
        $customFields = !empty($customFields) ? json_decode($customFields, true) : '';

        return view('audits/edit-modal', compact(
            'data',
            'users',
            'usersByRole',
            'getCompaniesAuthorized',
            'getAuditStatusTranslations',
            'customFields',
            )
        );
     }

     public function compose()
    {
        $getDepartmentsActive = getDepartmentsActive();

        $auditElements = array(
            array(
                'id' => 1,
                'label' => 'Tópico',
                'type' => 'text',
                'name' => 'topic',
                'icon' => 'ri-input-cursor-move',
                'placeholder' => 'Exemplo: "Este setor/departamento está organizado?"... "O abastecimento de produtos/insumos está em dia?"',
                'maxlenght' => '100',
            ),
            /*array(
                'id' => 2,
                'label' => 'Texto',
                'type' => 'textarea',
                'name' => 'description',
                'icon' => 'ri-file-text-line',
                'placeholder' => 'Efetue comentários/observações',
                'maxlenght' => '1000',
            ),*/
            /*array(
                'id' => 3,
                'label' => 'Checagem',
                'type' => 'checkbox',
                'name' => 'check',
                'icon' => 'ri-checkbox-fill',
                'placeholder' => 'Marque as opções',
                'maxlenght' => '',
            ),*/
            array(
                'id' => 4,
                'label' => 'Opções',
                'type' => 'radio',
                'name' => 'options',
                'icon' => 'ri-radio-button-line',
                'placeholder' => 'Escolha uma opção',
                'maxlenght' => '',
            ),
            /*array(
                'id' => 5,
                'label' => 'Seletor',
                'type' => 'select',
                'name' => 'selected',
                'icon' => 'ri-list-check',
                'placeholder' => '- Selecione -',
                'maxlenght' => '',
            ),*/
            /*array(
                'id' => 5,
                'label' => 'Upload',
                'type' => 'file',
                'name' => 'attachment',
                'icon' => 'ri-upload-cloud-2-fill',
                'placeholder' => 'Envie arquivos de imagem',
                'maxlenght' => '',
            )*/
        );


        return view('audits.compose', compact('getDepartmentsActive', 'auditElements'));
    }



}
