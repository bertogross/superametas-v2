<?php
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


?>
<div class="modal fade zoomIn" id="auditsEditModal" tabindex="-1"  aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header p-3 bg-info-subtle">
                <h5 class="modal-title" id="exampleModalLabel">Create Task</h5>
                <button type="button" class="btn-close btn-destroy" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form id="auditsForm" method="POST" autocomplete="off" class="needs-validation" novalidate>
                <?php echo csrf_field(); ?>

                <?php if(!empty($getCompaniesAuthorized) && is_array($getCompaniesAuthorized) && count($getCompaniesAuthorized) > 0): ?>
                    <div class="modal-body">

                        <input type="hidden" name="id" value="<?php echo e($auditId); ?>" />

                        <input type="hidden" name="status" value="<?php echo e($status); ?>" />
                        <input type="hidden" name="created_by" value="<?php echo e($created_by); ?>" />
                        <input type="hidden" name="current_user_editor" value="<?php echo e(auth()->id()); ?>" />

                        <div class="row g-3">
                            <div class="col-lg-6">
                                <label for="assigned_to" class="form-label">Loja</label>
                                <select class="form-select" name="assigned_to" id="assigned_to" required>
                                    <option <?php echo e(empty($assigned_to) ? 'selected' : ''); ?> value="">- Selecione -</option>
                                    <?php $__currentLoopData = $getCompaniesAuthorized; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $companyId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($companyId); ?>" <?php if(old('assigned_to', $assigned_to) == $companyId): echo 'selected'; endif; ?>><?php echo e(getCompanyAlias(intval($companyId))); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <div class="form-text">Selecione a loja que será vistoriada e auditada</div>
                            </div>

                            <div class="col-lg-6">
                                <label for="duedate-field" class="form-label">Data Limite</label>
                                <input type="text" name="due_date" class="form-control flatpickr-default" value=<?php echo e($due_date); ?>>
                                <div class="form-text">Opcional</div>
                            </div>

                            <!--end col-->
                            <div class="col-lg-6">
                                <label class="form-label">Atribuído a</label>
                                <div data-simplebar style="height: 130px;" class="bg-light p-3 rounded-2">
                                    <ul class="list-unstyled vstack gap-2 mb-0">
                                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li>
                                                <div class="form-check form-check-success d-flex align-items-center">
                                                    <input class="form-check-input me-3" type="radio" name="delegated_to"
                                                        value="<?php echo e($user->id); ?>" id="user-<?php echo e($user->id); ?>" <?php if(old('delegated_to', $delegated_to) == $user->id): echo 'checked'; endif; ?> required>
                                                    <label class="form-check-label d-flex align-items-center"
                                                        for="user-<?php echo e($user->id); ?>">
                                                        <span class="flex-shrink-0">
                                                            <img
                                                            <?php if(empty(trim($user->avatar))): ?>
                                                                src="<?php echo e(URL::asset('build/images/users/user-dummy-img.jpg')); ?>"
                                                            <?php else: ?>
                                                                src="<?php echo e(URL::asset('storage/' . $user->avatar)); ?>"
                                                            <?php endif; ?>
                                                                alt="<?php echo e($user->name); ?>" class="avatar-xxs rounded-circle">
                                                        </span>
                                                        <span class="flex-grow-1 ms-2"><?php echo e($user->name); ?></span>
                                                    </label>
                                                </div>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                                <div class="form-text">Selecione o colaborador que irá efetuar a vistoria</div>
                            </div>

                            <!--end col-->
                            <div class="col-lg-6">
                                <label class="form-label">Auditor(a)</label>
                                <div data-simplebar style="height: 130px;" class="bg-light p-3 rounded-2">
                                    <ul class="list-unstyled vstack gap-2 mb-0">
                                        <?php $__currentLoopData = $usersByRole; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $auditor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li>
                                                <div class="form-check form-check-success d-flex align-items-center">
                                                    <input class="form-check-input me-3" type="radio" name="audited_by"
                                                        value="<?php echo e($auditor->id); ?>" id="auditor-<?php echo e($auditor->id); ?>" <?php if(old('audited_by', $audited_by) == $auditor->id): echo 'checked'; endif; ?> required>
                                                    <label class="form-check-label d-flex align-items-center"
                                                        for="auditor-<?php echo e($auditor->id); ?>">
                                                        <span class="flex-shrink-0">
                                                            <img
                                                            <?php if(empty(trim($auditor->avatar))): ?>
                                                                src="<?php echo e(URL::asset('build/images/users/user-dummy-img.jpg')); ?>"
                                                            <?php else: ?>
                                                                src="<?php echo e(URL::asset('storage/' . $auditor->avatar)); ?>"
                                                            <?php endif; ?>
                                                                alt="<?php echo e($auditor->name); ?>" class="avatar-xxs rounded-circle">
                                                        </span>
                                                        <span class="flex-grow-1 ms-2"><?php echo e($auditor->name); ?></span>
                                                    </label>
                                                </div>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                                <div class="form-text">Selecione o colaborador que irá auditar a vistoria</div>
                            </div>

                            <div class="col-lg-12">
                                <label for="description" class="form-label">Observações</label>
                                <textarea name="description" class="form-control" maxlength="1000" id="description" rows="8"><?php echo e($description); ?></textarea>
                                <div class="form-text">Opcional</div>
                            </div>

                            <div class="col-lg-12">
                                <label for="custom-fields" class="form-label">Campos Personalizados</label>
                                <div id="custom-fields-container">
                                    <?php if($customFields): ?>
                                        <?php $__currentLoopData = $customFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="custom-field row mb-2">
                                                <div class="col">
                                                    <select name="custom_fields[<?php echo e($index); ?>][type]" class="form-select" placeholder="Tipo" required>
                                                        <option value="">Tipo</option>
                                                        <option value="text" <?php if($field['type'] == 'text'): echo 'selected'; endif; ?>>Texto</option>
                                                        <option value="date" <?php if($field['type'] == 'date'): echo 'selected'; endif; ?>>Data</option>
                                                        <option value="textarea" <?php if($field['type'] == 'textarea'): echo 'selected'; endif; ?>>Área de texto</option>
                                                        <option value="file" <?php if($field['type'] == 'file'): echo 'selected'; endif; ?>>Carregar arquivo</option>
                                                        <option value="checkbox" <?php if($field['type'] == 'checkbox'): echo 'selected'; endif; ?>>Checkbox</option>
                                                        <option value="radio" <?php if($field['type'] == 'radio'): echo 'selected'; endif; ?>>Radio Button</option>
                                                        <option value="select" <?php if($field['type'] == 'select'): echo 'selected'; endif; ?>>Selecionador</option>
                                                    </select>
                                                </div>
                                                <div class="col">
                                                    <input type="text" name="custom_fields[<?php echo e($index); ?>][name]" value="<?php echo e($field['name'] ?? ''); ?>" placeholder="nome_do_campo" class="form-control" maxlength="30"  data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Digite somente letras minúsculas e sem espaços" required>
                                                </div>
                                                <div class="col">
                                                    <input type="text" name="custom_fields[<?php echo e($index); ?>][label]" value="<?php echo e($field['label'] ?? ''); ?>" placeholder="Título do Campo" class="form-control" maxlength="50" required>
                                                </div>
                                                <div class="col-auto">
                                                    <button type="button" class="btn btn-ghost-danger btn-remove-custom-field"><i class="ri-delete-bin-3-line"></i></button>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
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
                <?php else: ?>
                    <div class="modal-body">
                        <div class="alert alert-warning">Lojas ainda não foram ativadas para seu perfil</div>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>
<?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views/audits/edit-modal.blade.php ENDPATH**/ ?>