@php
    $data = $data[0] ?? '';
    //APP_print_r($data);
    //APP_print_r($customFields);

    $auditId = $data->id ?? '';
    $created_by = $data->created_by ?? auth()->id();
    $assigned_to = $data->assigned_to ?? '';
    $delegated_to = $data->delegated_to ?? '';
    $audited_by = $data->audited_by ?? '';
    $due_date = $data->due_date ?? '';
    $due_date = !empty($due_date) && !is_null($due_date) ? date('d/m/Y', strtotime($due_date)) : date('d/m/Y', strtotime("+3 days"));
    $status = $data->status ?? 'pending';
    $description = $data->description ?? '';


@endphp
<div class="modal fade zoomIn" id="auditsEditModal" tabindex="-1"  aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header p-3 bg-info-subtle">
                <h5 class="modal-title" id="exampleModalLabel">Create Task</h5>
                <button type="button" class="btn-close btn-destroy" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form id="auditsForm" method="POST" autocomplete="off" class="needs-validation" novalidate>
                @csrf

                @if (!empty($getAuthorizedCompanies) && is_array($getAuthorizedCompanies) && count($getAuthorizedCompanies) > 0)
                    <div class="modal-body">

                        <input type="hidden" name="id" value="{{ $auditId }}" />

                        <input type="hidden" name="status" value="{{ $status }}" />
                        <input type="hidden" name="created_by" value="{{ $created_by }}" />
                        <input type="hidden" name="current_user_editor" value="{{ auth()->id() }}" />

                        <div class="row g-3">
                            <div class="col-lg-6">
                                <label for="assigned_to" class="form-label">Loja</label>
                                <select class="form-select" name="assigned_to" id="assigned_to" required>
                                    <option {{ empty($assigned_to) ? 'selected' : '' }} value="">- Selecione -</option>
                                    @foreach ($getAuthorizedCompanies as $companyId)
                                        <option value="{{ $companyId }}" @selected(old('assigned_to', $assigned_to) == $companyId)>{{ getCompanyAlias(intval($companyId)) }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Selecione a loja que será vistoriada e auditada</div>
                            </div>

                            <div class="col-lg-6">
                                <label for="duedate-field" class="form-label">Data Limite</label>
                                <input type="text" name="due_date" class="form-control flatpickr-default" value={{ $due_date }}>
                                <div class="form-text">Opcional</div>
                            </div>

                            <!--end col-->
                            <div class="col-lg-6">
                                <label class="form-label">Atribuído a</label>
                                <div data-simplebar style="height: 130px;" class="bg-light p-3 rounded-2">
                                    <ul class="list-unstyled vstack gap-2 mb-0">
                                        @foreach ($users as $user)
                                            <li>
                                                <div class="form-check form-check-success d-flex align-items-center">
                                                    <input class="form-check-input me-3" type="radio" name="delegated_to"
                                                        value="{{ $user->id }}" id="user-{{ $user->id }}" @checked(old('delegated_to', $delegated_to) == $user->id) required>
                                                    <label class="form-check-label d-flex align-items-center"
                                                        for="user-{{ $user->id }}">
                                                        <span class="flex-shrink-0">
                                                            <img
                                                            @if(empty(trim($user->avatar)))
                                                                src="{{ URL::asset('build/images/users/user-dummy-img.jpg') }}"
                                                            @else
                                                                src="{{ URL::asset('storage/' . $user->avatar) }}"
                                                            @endif
                                                                alt="{{ $user->name }}" class="avatar-xxs rounded-circle">
                                                        </span>
                                                        <span class="flex-grow-1 ms-2">{{ $user->name }}</span>
                                                    </label>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="form-text">Selecione o colaborador que irá efetuar a vistoria</div>
                            </div>

                            <!--end col-->
                            <div class="col-lg-6">
                                <label class="form-label">Auditor(a)</label>
                                <div data-simplebar style="height: 130px;" class="bg-light p-3 rounded-2">
                                    <ul class="list-unstyled vstack gap-2 mb-0">
                                        @foreach ($usersByRole as $auditor)
                                            <li>
                                                <div class="form-check form-check-success d-flex align-items-center">
                                                    <input class="form-check-input me-3" type="radio" name="audited_by"
                                                        value="{{ $auditor->id }}" id="auditor-{{ $auditor->id }}" @checked(old('audited_by', $audited_by) == $auditor->id) required>
                                                    <label class="form-check-label d-flex align-items-center"
                                                        for="auditor-{{ $auditor->id }}">
                                                        <span class="flex-shrink-0">
                                                            <img
                                                            @if(empty(trim($auditor->avatar)))
                                                                src="{{ URL::asset('build/images/users/user-dummy-img.jpg') }}"
                                                            @else
                                                                src="{{ URL::asset('storage/' . $auditor->avatar) }}"
                                                            @endif
                                                                alt="{{ $auditor->name }}" class="avatar-xxs rounded-circle">
                                                        </span>
                                                        <span class="flex-grow-1 ms-2">{{ $auditor->name }}</span>
                                                    </label>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="form-text">Selecione o colaborador que irá auditar a vistoria</div>
                            </div>

                            <div class="col-lg-12">
                                <label for="description" class="form-label">Observações</label>
                                <textarea name="description" class="form-control" maxlength="1000" id="description" rows="8">{{ $description }}</textarea>
                                <div class="form-text">Opcional</div>
                            </div>

                            <div class="col-lg-12">
                                <label for="custom-fields" class="form-label">Campos Personalizados</label>
                                <div id="custom-fields-container">
                                    @if ($customFields)
                                        @foreach ($customFields as $index => $field)
                                            <div class="custom-field row mb-2">
                                                <div class="col">
                                                    <select name="custom_fields[{{ $index }}][type]" class="form-select" placeholder="Tipo" required>
                                                        <option value="">Tipo</option>
                                                        <option value="text" @selected($field['type'] == 'text')>Texto</option>
                                                        <option value="date" @selected($field['type'] == 'date')>Data</option>
                                                        <option value="textarea" @selected($field['type'] == 'textarea')>Área de texto</option>
                                                        <option value="file" @selected($field['type'] == 'file')>Carregar arquivo</option>
                                                        <option value="checkbox" @selected($field['type'] == 'checkbox')>Checkbox</option>
                                                        <option value="radio" @selected($field['type'] == 'radio')>Radio Button</option>
                                                        <option value="select" @selected($field['type'] == 'select')>Selecionador</option>
                                                    </select>
                                                </div>
                                                <div class="col">
                                                    <input type="text" name="custom_fields[{{ $index }}][name]" value="{{ $field['name'] ?? '' }}" placeholder="nome_do_campo" class="form-control" maxlength="30"  data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Digite somente letras minúsculas e sem espaços" required>
                                                </div>
                                                <div class="col">
                                                    <input type="text" name="custom_fields[{{ $index }}][label]" value="{{ $field['label'] ?? '' }}" placeholder="Título do Campo" class="form-control" maxlength="50" required>
                                                </div>
                                                <div class="col-auto">
                                                    <button type="button" class="btn btn-ghost-danger btn-remove-custom-field"><i class="ri-delete-bin-3-line"></i></button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <button type="button" id="add-custom-field" class="btn btn-sm btn-soft-dark btn-border float-end" title="Adicionar Campo Personalizado"><i class="ri-add-line align-bottom me-1"></i> Campo Personalizado</button>
                            </div>


                        </div>
                        <!--end row-->
                    </div>
                    <div class="modal-footer wrap-form-btn d-none">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="button" class="btn btn-theme" id="btn-audits-update"></button>
                        </div>
                    </div>
                @else
                    <div class="modal-body">
                        <div class="alert alert-warning">Lojas ainda não foram ativadas para seu perfil</div>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
