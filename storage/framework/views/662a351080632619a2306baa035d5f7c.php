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
        $userId = $survey->user_id ?? auth()->id();
        $assigned_to = $survey->assigned_to ?? '';
        $delegated_to = $survey->delegated_to ?? '';
        $audited_by = $survey->audited_by ?? '';
        $start_date = $survey->start_date ?? '';
        $start_date = !empty($start_date) && !is_null($start_date) ? date('d/m/Y', strtotime($start_date)) : date('d/m/Y', strtotime("+3 days"));
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

                    <a href="<?php echo e(route('surveysShowURL', ['id' => $survey->id])); ?>" class="btn btn-sm btn-outline-dark" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Visualizar em nova guia" target="_blank" tabindex="-1">Visualizar</a>
                <?php else: ?>
                    <button type="button" class="btn btn-sm btn-theme" id="btn-surveys-store-or-update" tabindex="-1">Salvar</button>
                <?php endif; ?>
            </div>
            <h4 class="card-title mb-0"><i class="ri-drag-drop-line fs-16 align-middle text-theme me-2"></i>Atribuições</h4>
         </div>
        <div class="card-body">
            <form id="surveysForm" method="POST" class="needs-validation" novalidate autocomplete="off">
                <?php echo csrf_field(); ?>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-5 col-xxl-4">
                        <div class="p-3 border border-1 border-light rounded">
                            <input type="hidden" name="id" value="<?php echo e($surveyId); ?>">

                            <input type="hidden" name="status" value="<?php echo e($status); ?>">

                            <div class="mb-4">
                                <label for="title" class="form-label">Título:</label>
                                <input type="text" id="title" name="title" class="form-control" value="<?php echo e($dateRange); ?>" maxlength="100" required>
                                <div class="form-text">
                                    Exemplo: Checklist Abertura de Loja
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="date-range-field" class="form-label">Tipo de Recorrência:</label>
                                <select class="form-select" name="recurring" required>
                                    <option selected>- Selecione -</option>
                                    <option value="once">Uma vez</option>
                                    <option value="daily">Diária</option>
                                    
                                </select>
                            </div>

                            

                            <div class="mb-4">
                                <label for="date-start-field" class="form-label">Data Inicial:</label>
                                <input type="text" id="date-start-field"" name="start_date" class="form-control flatpickr-default" value="<?php echo e($dateRange); ?>" maxlength="10" required>
                            </div>

                            <!--end col-->
                            <div class="mb-4">
                                <label class="form-label mb-0">Vistoria Atribuída a:</label>
                                <div class="form-text mt-0 mb-2">Selecione para cada das Lojas o colaborador que irá proceder com a <strong>Vistoria</strong></div>
                                <div class="row">
                                    <div class="col-sm-6 col-md-4 col-lg-4 col-xxl-3">
                                        <div class="nav nav-pills flex-column nav-pills-tab verti-nav-pills custom-verti-nav-pills nav-pills-theme" role="tablist" aria-orientation="vertical">
                                            <?php $__currentLoopData = $getAuthorizedCompanies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $companyId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <a class="nav-link text-uppercase <?php echo e($key == 0 ? 'active' : ''); ?> text-uppercase" data-bs-target="#delegated_to-company-<?php echo e($companyId); ?>" id="delegated_to-company-<?php echo e($companyId); ?>-tab" data-bs-toggle="pill" role="tab" aria-controls="v-pills-account" aria-selected="true"><?php echo e(getCompanyAlias($companyId)); ?></a>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-8 col-lg-8 col-xxl-9">
                                        <div class="tab-content p-0 bg-light h-100">
                                            <?php $__currentLoopData = $getAuthorizedCompanies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $companyId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="tab-pane fade show <?php echo e($key == 0 ? 'active' : ''); ?>" id="delegated_to-company-<?php echo e($companyId); ?>" role="tabpanel" aria-labelledby="delegated_to-company-<?php echo e($companyId); ?>-tab">
                                                    <div class="bg-light p-3 rounded-2">
                                                        <ul class="list-unstyled vstack gap-2 mb-0">
                                                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <?php
                                                                    $userId = $user->id;
                                                                    $userName = $user->name;
                                                                    $userAvatar = $user->avatar;

                                                                    $userCompanies = getAuthorizedCompanies($userId) ?? null;
                                                                ?>
                                                                <?php if( is_array($userCompanies) && in_array($companyId, $userCompanies) ): ?>
                                                                    <li>
                                                                        <div class="form-check form-check-success d-flex align-items-center">
                                                                            <input class="form-check-input me-3" type="radio" name="delegated_to[<?php echo e($companyId); ?>]"
                                                                                value="<?php echo e($userId); ?>" id="delegated_to-user-<?php echo e($companyId.$userId); ?>" <?php if(old('delegated_to', $delegated_to) == $userId): echo 'checked'; endif; ?> required>
                                                                            <label class="form-check-label d-flex align-items-center"
                                                                                for="delegated_to-user-<?php echo e($companyId.$userId); ?>">
                                                                                <span class="flex-shrink-0">
                                                                                    <img
                                                                                    <?php if(empty(trim($userAvatar))): ?>
                                                                                        src="<?php echo e(URL::asset('build/images/users/user-dummy-img.jpg')); ?>"
                                                                                    <?php else: ?>
                                                                                        src="<?php echo e(URL::asset('storage/' . $userAvatar)); ?>"
                                                                                    <?php endif; ?>
                                                                                        alt="<?php echo e($userName); ?>" class="avatar-xxs rounded-circle">
                                                                                </span>
                                                                                <span class="flex-grow-1 ms-2"><?php echo e($userName); ?></span>
                                                                            </label>
                                                                        </div>
                                                                    </li>
                                                                <?php endif; ?>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--end col-->
                            <div class="mb-4">
                                <label class="form-label mb-0">Auditora Atribuída a:</label>
                                <div class="form-text mt-0 mb-2">Selecione para cada das Lojas o colaborador que irá <strong>Auditar</strong> a vistoria</div>
                                <div class="row">
                                    <div class="col-sm-6 col-md-4 col-lg-4 col-xxl-3">
                                        <div class="nav nav-pills flex-column nav-pills-tab verti-nav-pills custom-verti-nav-pills nav-pills-theme" role="tablist" aria-orientation="vertical">
                                            <?php $__currentLoopData = $getAuthorizedCompanies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $companyId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <a class="nav-link text-uppercase <?php echo e($key == 0 ? 'active' : ''); ?> text-uppercase" data-bs-target="#audited_by-company-<?php echo e($companyId); ?>" id="audited_by-company-<?php echo e($companyId); ?>-tab" data-bs-toggle="pill" role="tab" aria-controls="v-pills-account" aria-selected="true"><?php echo e(getCompanyAlias($companyId)); ?></a>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-8 col-lg-8 col-xxl-9">
                                        <div class="tab-content p-0 bg-light h-100">
                                            <?php $__currentLoopData = $getAuthorizedCompanies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $companyId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="tab-pane fade show <?php echo e($key == 0 ? 'active' : ''); ?>" id="audited_by-company-<?php echo e($companyId); ?>" role="tabpanel" aria-labelledby="audited_by-company-<?php echo e($companyId); ?>-tab">
                                                    <div class="bg-light p-3 rounded-2">
                                                        <ul class="list-unstyled vstack gap-2 mb-0">
                                                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <?php
                                                                    $userId = $user->id;
                                                                    $userName = $user->name;
                                                                    $userAvatar = $user->avatar;

                                                                    $userCompanies = getAuthorizedCompanies($userId) ?? null;
                                                                ?>
                                                                <?php if( is_array($userCompanies) && in_array($companyId, $userCompanies) ): ?>
                                                                    <li>
                                                                        <div class="form-check form-check-success d-flex align-items-center">
                                                                            <input class="form-check-input me-3" type="radio" name="audited_by[<?php echo e($companyId); ?>]"
                                                                                value="<?php echo e($userId); ?>" id="audited_by-user-<?php echo e($companyId.$userId); ?>" <?php if(old('audited_by', $delegated_to) == $userId): echo 'checked'; endif; ?> required>
                                                                            <label class="form-check-label d-flex align-items-center"
                                                                                for="audited_by-user-<?php echo e($companyId.$userId); ?>">
                                                                                <span class="flex-shrink-0">
                                                                                    <img
                                                                                    <?php if(empty(trim($userAvatar))): ?>
                                                                                        src="<?php echo e(URL::asset('build/images/users/user-dummy-img.jpg')); ?>"
                                                                                    <?php else: ?>
                                                                                        src="<?php echo e(URL::asset('storage/' . $userAvatar)); ?>"
                                                                                    <?php endif; ?>
                                                                                        alt="<?php echo e($userName); ?>" class="avatar-xxs rounded-circle">
                                                                                </span>
                                                                                <span class="flex-grow-1 ms-2"><?php echo e($userName); ?></span>
                                                                            </label>
                                                                        </div>
                                                                    </li>
                                                                <?php endif; ?>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="description" class="form-label">Observações:</label>
                                <textarea name="description" class="form-control" maxlength="1000" id="description" rows="7" maxlength="500"><?php echo e($description); ?></textarea>
                                <div class="form-text">Opcional</div>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-7 col-xxl-8">
                        <div class="p-3 border border-1 border-light rounded">

                            <p class="text-ody mb-0">Composição do formulário:</p>

                            <div id="nested-compose-area" style="min-height: 250px;">
                                <div class="accordion list-group nested-list nested-receiver"><?php if($Default || $Custom): ?>
                                    <?php if($Default): ?>
                                        <?php $__env->startComponent('surveys.components.template-form'); ?>
                                            <?php $__env->slot('type', 'default'); ?>
                                            <?php $__env->slot('data', $Default); ?>
                                        <?php echo $__env->renderComponent(); ?>
                                    <?php endif; ?>

                                    <?php if($Custom): ?>
                                        <?php $__env->startComponent('surveys.components.template-form'); ?>
                                            <?php $__env->slot('type', 'custom'); ?>
                                            <?php $__env->slot('data', $Custom); ?>
                                        <?php echo $__env->renderComponent(); ?>
                                    <?php endif; ?>
                                <?php endif; ?></div>

                                <div class="clearfix mt-3">
                                    <button type="button" class="btn btn-ghost-dark btn-icon rounded-pill float-end cursor-crosshair" id="btn-add-block" tabindex="-1" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Adicionar Bloco"><i class="ri-folder-add-line text-theme fs-4"></i></button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('build/libs/choices.js/public/assets/scripts/choices.min.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('build/libs/sortablejs/Sortable.min.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('build/libs/flatpickr/flatpickr.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/flatpickr/l10n/pt.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/flatpickr/plugins/monthSelect/index.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/flatpickr/plugins/confirmDate/confirmDate.js')); ?>"></script>

    <script>
        var surveysEditURL = "<?php echo e(route('surveysEditURL')); ?>";
        var surveysShowURL = "<?php echo e(route('surveysShowURL')); ?>";
        var surveysStoreOrUpdateURL = "<?php echo e(route('surveysStoreOrUpdateURL')); ?>";

        //var surveysTermsSearchURL = "<?php echo e(route('surveysTermsSearchURL')); ?>";
        //var surveysTermsStoreOrUpdateURL = "<?php echo e(route('surveysTermsStoreOrUpdateURL')); ?>";

        //var choicesSelectorClass = ".surveys-term-choice";
    </script>
    <script src="<?php echo e(URL::asset('build/js/surveys.js')); ?>" type="module"></script>

    <script src="<?php echo e(URL::asset('build/js/surveys-sortable.js')); ?>" type="module"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views\surveys\create.blade.php ENDPATH**/ ?>