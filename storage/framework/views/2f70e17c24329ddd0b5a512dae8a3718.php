<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.surveys'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('url'); ?>
            <?php echo e(route('surveysIndexURL')); ?>

        <?php $__env->endSlot(); ?>
        <?php $__env->slot('li_1'); ?>
            <?php echo app('translator')->get('translation.surveys'); ?>
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            <?php if($survey): ?>
                Edição de Vistoria<small><i class="ri-arrow-drop-right-fill text-theme ms-2 me-2 align-bottom"></i> #<span class="text-theme"><?php echo e($survey->id); ?></span> </small>
            <?php else: ?>
                Cadastrar Vistoria
            <?php endif; ?>
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <?php echo $__env->make('components.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php
        $surveyId = $survey->id ?? '';
        $created_by = $survey->created_by ?? auth()->id();
        $assigned_to = $survey->assigned_to ?? '';
        $delegated_to = $survey->delegated_to ?? '';
        $audited_by = $survey->audited_by ?? '';
        $due_date = $survey->due_date ?? '';
        $due_date = !empty($due_date) && !is_null($due_date) ? date('d/m/Y', strtotime($due_date)) : date('d/m/Y', strtotime("+3 days"));
        $dateRange = '';
        $status = $survey->status ?? 'pending';
        $description = $survey->description ?? '';
        //appPrintR($survey);
    ?>

    <?php if(!empty($getAuthorizedCompanies) && is_array($getAuthorizedCompanies) && count($getAuthorizedCompanies) > 0): ?>
    <?php else: ?>
        <div class="alert alert-warning">Lojas ainda não foram ativadas para seu perfil</div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <div class="float-end">
                <?php if($survey): ?>
                    <button type="button" class="btn btn-sm btn-outline-theme" id="btn-surveys-store-or-update" tabindex="-1">Atualizar</button>

                    <button type="button" class="btn btn-sm btn-outline-info" id="btn-surveys-clone" tabindex="-1">Clonar</button>
                <?php else: ?>
                    <button type="button" class="btn btn-sm btn-theme" id="btn-surveys-store-or-update" tabindex="-1">Salvar</button>
                <?php endif; ?>

                <a href="" class="btn btn-sm btn-outline-theme" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Visualizar em nova guia" target="_blank" tabindex="-1">Visualizar</a>
            </div>
            <h4 class="card-title mb-0"><i class="ri-drag-drop-line fs-16 align-middle text-theme me-2"></i>Formulário</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-6 col-md-5 col-lg-4 col-xxl-3">
                    <div class="p-3 border border-1 border-light rounded">
                        <form id="surveysForm" method="POST" autocomplete="off" class="needs-validation" novalidate autocomplete="false">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="id" value="<?php echo e($surveyId); ?>" />

                            <input type="hidden" name="status" value="<?php echo e($status); ?>" />
                            <input type="hidden" name="created_by" value="<?php echo e($created_by); ?>" />
                            <input type="hidden" name="current_user_editor" value="<?php echo e(auth()->id()); ?>" />


                            <div class="mb-3">
                                <label for="assigned_to" class="form-label">Loja</label>
                                <select class="form-select" name="assigned_to" id="assigned_to" required>
                                    <option <?php echo e(empty($assigned_to) ? 'selected' : ''); ?> value="">- Selecione -</option>
                                    <?php $__currentLoopData = $getAuthorizedCompanies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $companyId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($companyId); ?>" <?php if(old('assigned_to', $assigned_to) == $companyId): echo 'selected'; endif; ?>><?php echo e(getCompanyAlias(intval($companyId))); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <div class="form-text">Selecione a loja que será vistoriada e auditada</div>
                            </div>

                            <div class="mb-3">
                                <label for="date-range-field" class="form-label">Data Inicial e Limite</label>
                                <input type="text" id="date-range" name="date_range" class="form-control flatpickr-range" value="<?php echo e($dateRange); ?>" required>
                                <div class="form-text d-none">Opcional</div>
                            </div>

                            

                            <div>
                                <label for="description" class="form-label">Observações</label>
                                <textarea name="description" class="form-control" maxlength="1000" id="description" rows="8"><?php echo e($description); ?></textarea>
                                <div class="form-text">Opcional</div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-sm-6 col-md-7 col-lg-8 col-xxl-9">
                    <div class="p-3 border border-1 border-light rounded">
                        <p class="text-muted mb-2">Use <code>arrow-navtabs </code>class to create arrow nav tabs.</p>
                        <ul class="nav nav-tabs nav-border-top nav-border-top-theme mb-0" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#arrow-departments" role="tab">
                                    <span class="d-block d-sm-none"><i class="mdi mdi-home-variant"></i></span>
                                    <span class="d-none d-sm-block">Departamentos</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#arrow-custom" role="tab">
                                    <span class="d-block d-sm-none"><i class="mdi mdi-account"></i></span>
                                    <span class="d-none d-sm-block">Formulário Customizado</span>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content text-muted p-3 border border-1 border-light rounded rounded-top-0">
                            <div class="tab-pane active" id="arrow-departments" role="tabpanel">
                                <h6>Departamentos</h6>
                                <div class="mb-3">
                                    <select class="form-select" name="survey_compose_default_id" id="survey_compose_default_id">
                                        <option value="">- Selecionar Formulário -</option>
                                        <?php $__currentLoopData = $getSurveyComposeDefault; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $default): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($default->id); ?>" <?php if(old('survey_compose_default_id', $surveyComposeDefaultId ?? '') == $default->id): echo 'selected'; endif; ?>>
                                                <?php echo e($default->title); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div id="load-default-form" class="border border-1 rounded border-light p-3"></div>
                            </div>
                            <div class="tab-pane" id="arrow-custom" role="tabpanel">
                                <h6>Formulário Customizado</h6>
                                <div class="mb-3">
                                    <select class="form-select" name="survey_compose_custom_id" id="survey_compose_custom_id">
                                        <option value="">- Selecionar Formulário -</option>
                                        <?php $__currentLoopData = $getSurveyComposeCustom; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $custom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($custom->id); ?>" <?php if(old('survey_compose_custom_id', $surveyComposeCustomId ?? '') == $custom->id): echo 'selected'; endif; ?>>
                                                <?php echo e($custom->title); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div id="load-custom-form" class="border border-1 rounded border-light p-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('build/libs/sortablejs/Sortable.min.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('build/libs/choices.js/public/assets/scripts/choices.min.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('build/libs/sweetalert2/sweetalert2.min.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('build/libs/flatpickr/flatpickr.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/flatpickr/l10n/pt.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/flatpickr/plugins/monthSelect/index.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/flatpickr/plugins/confirmDate/confirmDate.js')); ?>"></script>

    <script>
        var surveysEditURL = "<?php echo e(route('surveysEditURL')); ?>";
        var surveysCreateOrUpdateURL = "<?php echo e(route('surveysCreateOrUpdateURL')); ?>";
    </script>
    <script src="<?php echo e(URL::asset('build/js/surveys.js')); ?>" type="module"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/surveys/create.blade.php ENDPATH**/ ?>