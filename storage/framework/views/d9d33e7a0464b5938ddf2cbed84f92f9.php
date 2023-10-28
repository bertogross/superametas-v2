<?php $__env->startSection('title'); ?>
    Title HERE
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            <?php echo app('translator')->get('translation.session'); ?>
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?> Google Drive  <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>


    <a href="<?php echo e(url('/google-drive/redirect')); ?>" class="btn btn-primary">Authorize Google Drive</a>


<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views/google-drive/authorize.blade.php ENDPATH**/ ?>