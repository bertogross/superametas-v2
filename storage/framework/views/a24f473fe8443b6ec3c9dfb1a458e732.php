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
            Composição
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

        <div class="row">
            <div class="col-xxl-9">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0"><i class="ri-drag-drop-line fs-16 align-middle text-theme me-2"></i>Área do Formulário</h4>
                    </div><!-- end card header -->

                    <div id="nested-compose-area" class="card-body">
                        <p class="text-muted">
                            Componha o formulário arrastando os elementos para esta área.
                        </p>
                        <div class="list-group nested-list nested-receiver">

                            <?php $__currentLoopData = $getDepartmentsActive; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="list-group-item nested-1 bg-dark-subtle" data-dep="<?php echo e($department->department_id); ?>" draggable="false">
                                    <?php echo e($department->department_alias); ?>

                                    <div class="list-group nested-list">
                                        <div class="list-group-item nested-2">Analytics</div>
                                        <div class="list-group-item nested-2">CRM</div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </div>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div>

            <div class="col-xxl-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0"><i class="ri-bring-to-front fs-16 align-middle text-theme me-2"></i>Elementos</h4>
                    </div><!-- end card header -->

                    <div id="nested-elements-area" class="card-body">
                        <p class="text-muted">
                            Aqui estão os elementos de composição.<br>
                            Você poderá complementar o formulário arrastando para a posição desejada.
                        </p>

                        <div class="list-group nested-list">
                            <div class="nested-element">
                                <div class="list-group-item bg-light-subtle nested-1">
                                    <div class="clearfix">
                                        <button type="button" class="btn btn-ghost-danger float-end btn-remove-element pt-0 pb-0"><i class="ri-delete-bin-2-line"></i></button>
                                        <span class="label-element">
                                            Bloco dos Elementos
                                        </span>
                                        <input type="text" class="form-control" name="audit_compose[]['title']" placeholder="Informe o Título / Setor / Departamento">
                                    </div>

                                    <div class="list-group-item nested-1 nested-receiver-block-fake" style="min-height: 50px;"></div>

                                    <div class="list-group-item nested-1 nested-receiver-block" style="min-height: 50px;"></div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <?php $__currentLoopData = $auditElements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-xxl-6 col-md-12 nested-element-to-block">
                                        <div class="list-group-item bg-light-subtle nested-1">
                                            <div class="clearfix">
                                                <button type="button" class="btn btn-ghost-danger float-end btn-remove-element pt-0 pb-0"><i class="ri-delete-bin-2-line"></i></button>

                                                <?php echo e($element['label']); ?>

                                            </div>

                                            <div class="list-group nested-list">
                                                <div class="list-group-item nested-2">
                                                    <?php echo e($element['type']); ?>

                                                </div>
                                                <div class="list-group-item nested-2">
                                                    <?php echo e($element['name']); ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div>
        </div>

    <?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('build/libs/choices.js/public/assets/scripts/choices.min.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('build/libs/sweetalert2/sweetalert2.min.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('build/libs/flatpickr/flatpickr.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/flatpickr/l10n/pt.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/flatpickr/plugins/monthSelect/index.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/flatpickr/plugins/confirmDate/confirmDate.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('build/libs/sortablejs/Sortable.min.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('build/js/audits.js')); ?>" type="module"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views/audits/compose.blade.php ENDPATH**/ ?>