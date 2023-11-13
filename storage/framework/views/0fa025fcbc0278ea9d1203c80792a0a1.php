<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.api-keys'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('url'); ?>
            <?php echo e(url('settings')); ?>

        <?php $__env->endSlot(); ?>
        <?php $__env->slot('li_1'); ?>
            <?php echo app('translator')->get('translation.settings'); ?>
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            <?php echo app('translator')->get('translation.api-keys'); ?>
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <?php echo $__env->make('components.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="table-responsive border border-1 border-light rounded">
        <table class="table align-middle table-hover table-striped table-nowrap mb-0">
            <thead class="table-light text-uppercase">
                <tr>
                    <th>Origem</th>
                    <th>Conta de</th>
                    <th>Conectada por</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="list form-check-all">
                <tr>
                    <td>Sysmo API</td>
                    <td>
                        -
                    </td>
                    <td>
                        -
                    </td>
                    <td>
                        <span class="badge bg-success-subtle text-success">Ativo</span>
                    </td>
                    <td class="text-end">
                    </td>
                </tr>
                <tr>
                    <td>Dropbox API</td>
                    <td><?php echo e(!empty($DropBoxUserAccountInfo) ? $DropBoxUserAccountInfo['name']['display_name'] : '-'); ?></td>
                    <td>
                        <?php echo e(!empty($DropBoxUserAccountInfo) ? $DropBoxUserAccountInfo['email'] : '-'); ?>

                    </td>
                    <td>
                        <?php if( empty($DropBoxUserAccountInfo) || $DropBoxUserAccountInfo['disabled'] ): ?>
                            <span class="badge bg-danger-subtle text-danger">Desativado</span>
                        <?php else: ?>
                            <span class="badge bg-success-subtle text-success">Ativo</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <?php if( getDropboxToken() && !empty($DropBoxUserAccountInfo)): ?>
                            <a class="btn btn-sm btn-outline-danger" href="<?php echo e(route('DropboxDeauthorizeURL')); ?>">
                                Desconectar Dropbox
                            </a>

                            <a href="<?php echo e(route('DropboxIndexURL')); ?>" class="btn btn-sm btn-outline-theme" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Visualizar os Arquivos"><i class="ri-folder-open-line"></i></a>
                        <?php else: ?>
                            <a class="btn btn-sm btn-primary" target="_blank" href="<?php echo e(route('DropboxAuthorizeURL')); ?>">
                                Conectar ao Dropbox
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                
            </tbody>
        </table>
    </div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views\settings\api-keys.blade.php ENDPATH**/ ?>