<?php $__env->startSection('title'); ?>
    Composição de Auditorias
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
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
            Composições
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="card">
        <div class="card-header border-0">
            <div class="d-flex align-items-center">
                <h5 class="card-title mb-0 flex-grow-1">Listagem</h5>
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
        
        <div class="card-body">
            <div class="table-responsive table-card mb-4">
                <table class="table align-middle table-nowrap mb-0 table-striped table-hover" id="composeTable">
                    <thead class="table-light text-muted text-uppercase">
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Registrado</th>
                            <th>Atualizado</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $composes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compose): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($compose->id); ?></td>
                                <td>
                                    <?php echo e($compose->title); ?>

                                </td>
                                <td>
                                    <?php echo e($compose->created_at ? \Carbon\Carbon::parse($compose->created_at)->format('d F, Y') : '-'); ?>

                                </td>
                                <td>
                                    <?php echo e($compose->updated_at ? \Carbon\Carbon::parse($compose->updated_at)->format('d F, Y') : '-'); ?>

                                </td>
                                <td>
                                    <?php echo statusBadge($compose->status); ?>

                                </td>
                                <td scope="row" class="text-end">
                                    <div class="btn-group">
                                        <a href="<?php echo e(route('auditsComposeEditURL', $compose->id)); ?>" class="btn btn-sm btn-outline-dark" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-line"></i></a>

                                        <a href="<?php echo e(route('auditsComposeShowURL', $compose->id)); ?>" class="btn btn-sm btn-outline-dark" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Visualizar"><i class="ri-eye-line"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views/audits/compose/index.blade.php ENDPATH**/ ?>