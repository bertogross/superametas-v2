<?php
// APP_print_r($getAuditStatusTranslations);

?>

<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.audits'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(URL::asset('build/libs/sweetalert2/sweetalert2.min.css')); ?>" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('url'); ?>
            <?php echo e(route('auditsIndexURL')); ?>

        <?php $__env->endSlot(); ?>
        <?php $__env->slot('li_1'); ?>
            <?php echo app('translator')->get('translation.audits'); ?>
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            <?php echo app('translator')->get('translation.list'); ?>
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <?php $__currentLoopData = $getAuditStatusTranslations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col">
            <div class="card card-animate" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="bottom" title="<?php echo e($value['description']); ?>">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="fw-medium text-muted mb-0"><?php echo e($value['label']); ?></p>
                            <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="<?php echo e($auditStatusCount[$key] ?? 0); ?>"></span></h2>
                            
                        </div>
                        <div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-<?php echo e($value['color']); ?>-subtle text-<?php echo e($value['color']); ?> rounded-circle fs-4">
                                    <i class="<?php echo e(!empty($value['icon']) ? $value['icon'] : 'ri-ticket-2-line'); ?>"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div>
        </div>
        <!--end col-->
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <!--end row-->


    <div class="card" id="tasksList">
        <div class="card-header border-0">
            <div class="d-flex align-items-center">
                <h5 class="card-title mb-0 flex-grow-1">Auditorias</h5>
                <div class="flex-shrink-0">
                    <div class="d-flex flex-wrap gap-2">
                        <?php $__env->startComponent('components.audits-nav'); ?>
                            <?php $__env->slot('url'); ?>
                                <?php echo e(route('auditsIndexURL')); ?>

                            <?php $__env->endSlot(); ?>
                        <?php echo $__env->renderComponent(); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body border border-dashed border-end-0 border-start-0">
            <form action="<?php echo e(route('auditsIndexURL')); ?>" method="get" autocomplete="off">
                <div class="row g-3">
                    

                    <div class="col-sm-12 col-md col-lg">
                        <div class="input-light">
                            <select class="form-select" data-choices data-choices-removeItem name="delegated_to[]" multiple data-placeholder="Atribuíção">
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($user->id); ?>" <?php echo e(in_array($user->id, $delegated_to) ? 'selected' : ''); ?>><?php echo e($user->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md col-lg">
                        <div class="input-light">
                            <select class="form-select" data-choices data-choices-removeItem name="audited_by[]" multiple data-placeholder="Auditoria">
                                <?php $__currentLoopData = $usersByRole; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $auditor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($auditor->id); ?>" <?php echo e(in_array($auditor->id, $audited_by) ? 'selected' : ''); ?>><?php echo e($auditor->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md col-lg">
                        <input type="text" class="form-control bg-light border-light flatpickr-range" name="created_at" placeholder="Período do Registro" value="<?php echo e(request('created_at')); ?>">
                    </div>

                    <div class="col-sm-12 col-md col-lg">
                        <div class="input-light">
                            <select class="form-select" name="status">
                                <option value="" <?php echo e(!request('status') ? 'selected' : ''); ?>>Status</option>
                                <?php $__currentLoopData = $getAuditStatusTranslations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>" <?php echo e(request('status') == $key ? 'selected' : ''); ?>><?php echo e($value['label']); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-auto col-lg-auto wrap-form-btn">
                        <button type="submit" class="btn btn-theme w-100 init-loader"> <i class="ri-equalizer-fill me-1 align-bottom"></i> Filtrar</button>
                    </div>

                </div>
                <!--end row-->
            </form>
        </div>
        <!--end card-body-->
        <div class="card-body">
            <div class="table-responsive table-card mb-4">
                <table class="table align-middle table-nowrap mb-0 table-striped table-hover" id="tasksTable">
                    <thead class="table-light text-muted text-uppercase">
                        <tr>
                            <th scope="col">ID</th>
                            <th class="sort" data-sort="created_at" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Data de Registro">Registro</th>
                            <th class="sort" data-sort="created_at" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Data de Execução da Vistoria">Vistoria</th>
                            <th class="sort" data-sort="created_at" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Data de Execução da Auditoria">Auditoria</th>
                            <th class="sort" data-sort="delegated_to" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Colaborador ao qual foi atribuída a tarefa de Vistoria">Atribuído a</th>
                            <th class="sort" data-sort="assigned_to" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="A loja em que a tarefa será/foi desempenhada">Loja</th>
                            <th class="sort" data-sort="status" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Status: Pendente, Em Andamento, Concluído, Auditado">Status</th>
                            
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $audits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $audit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td scope="row"><a class="fw-medium link-primary" href="<?php echo e(route('auditsShowURL', $audit->id)); ?>"><?php echo e($audit->id); ?></a></td>
                                <td class="created_at">
                                    <?php echo e($audit->created_at ? \Carbon\Carbon::parse($audit->created_at)->format('d F, Y') : '-'); ?>

                                </td>
                                <td class="completed_at">
                                    <?php echo e($audit->completed_at ? \Carbon\Carbon::parse($audit->completed_at)->format('d F, Y') : '-'); ?>

                                </td>
                                <td class="audited_at">
                                    <?php echo e($audit->audited_at ? \Carbon\Carbon::parse($audit->audited_at)->format('d F, Y') : '-'); ?>

                                </td>
                                <td class="delegated_to align-middle">
                                    <?php if($audit->delegated_to): ?>
                                        <?php
                                            $avatar = getUserData($audit->delegated_to)['avatar'];
                                            $name = getUserData($audit->delegated_to)['name'];
                                        ?>
                                        <a href="javascript: void(0);" class="avatar-group-item me-1" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" aria-label="<?php echo e($name); ?>" title="<?php echo e($name); ?>">
                                            <img
                                            <?php if(empty(trim($avatar))): ?>
                                                src="<?php echo e(URL::asset('build/images/users/user-dummy-img.jpg')); ?>"
                                            <?php else: ?>
                                                src="<?php echo e(URL::asset('storage/' .$avatar )); ?>"
                                            <?php endif; ?>
                                            alt="<?php echo e($name); ?>" class="rounded-circle avatar-xxs">
                                        </a> <?php echo e($name); ?>

                                    <?php endif; ?>
                                </td>
                                <td class="assigned_to">
                                    <?php echo e(getCompanyAlias($audit->assigned_to)); ?>

                                </td>
                                <td class="status">
                                    <span class="badge bg-<?php echo e($getAuditStatusTranslations[$audit->status]['color']); ?>-subtle text-<?php echo e($getAuditStatusTranslations[$audit->status]['color']); ?> text-uppercase" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="<?php echo e($getAuditStatusTranslations[$audit->status]['description']); ?>">
                                        <?php echo e($getAuditStatusTranslations[$audit->status]['label']); ?>

                                    </span>

                                </td>
                                
                                <td scope="row" class="text-end">
                                    <div class="btn-group">
                                        <?php if($audit->status == 'pending' ): ?>
                                            <button type="button" data-audit-id="<?php echo e($audit->id); ?>" class="btn btn-sm btn-outline-dark waves-effect btn-audit-edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-line"></i></button>
                                        <?php else: ?>
                                            <button type="button" disabled class="btn btn-sm btn-outline-dark cursor-not-allowed" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-title="Edição Bloqueada" data-bs-content="Status <b class='text-<?php echo e($getAuditStatusTranslations[$audit->status]['color']); ?>'><?php echo e($getAuditStatusTranslations[$audit->status]['label']); ?></b><br><br>A edição será possível somente se o usuário ao qual foi atribuída tal tarefa optar por <b>Abortar</b>"><i class="ri-edit-line"></i></button>
                                        <?php endif; ?>
                                        <a href="<?php echo e(route('auditsShowURL', $audit->id)); ?>" class="btn btn-sm btn-outline-dark waves-effect" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Visualizar"><i class="ri-eye-line"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>

            </div>
            <div class="d-flex justify-content-center">
                <?php echo $audits->links('layouts.custom-pagination'); ?>

            </div>

        </div>
        <!--end card-body-->
    </div>


<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('build/libs/choices.js/public/assets/scripts/choices.min.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('build/libs/sweetalert2/sweetalert2.min.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('build/libs/flatpickr/flatpickr.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/flatpickr/l10n/pt.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/flatpickr/plugins/monthSelect/index.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/flatpickr/plugins/confirmDate/confirmDate.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('build/js/audits.js')); ?>" type="module"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views/audits/index.blade.php ENDPATH**/ ?>