<!-- ========== App Menu ========== -->
<?php
    $getCompanyLogo = getCompanyLogo();
?>
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="<?php echo e(url('/')); ?>" class="logo logo-dark" title="Ir para inicial do <?php echo e(env('APP_NAME')); ?>">
            <span class="logo-sm">
                <img src="<?php echo e(URL::asset('build/images/logo-sm.png')); ?>" alt="<?php echo e(env('APP_NAME')); ?>" height="22">
            </span>
            <span class="logo-lg">
                <img
                <?php if($getCompanyLogo): ?>
                    src="<?php echo e($getCompanyLogo); ?>"
                <?php else: ?>
                    src="<?php echo e(URL::asset('build/images/logo-dark.png')); ?>"
                <?php endif; ?>
                alt="<?php echo e(env('APP_NAME')); ?>" height="39">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="<?php echo e(url('/')); ?>" class="logo logo-light" title="Ir para inicial do <?php echo e(env('APP_NAME')); ?>">
            <span class="logo-sm">
                <img src="<?php echo e(URL::asset('build/images/logo-sm.png')); ?>" alt="<?php echo e(env('APP_NAME')); ?>" height="22">
            </span>
            <span class="logo-lg">
                <img
                <?php if($getCompanyLogo): ?>
                    src="<?php echo e($getCompanyLogo); ?>"
                <?php else: ?>
                    src="<?php echo e(URL::asset('build/images/logo-light.png')); ?>"
                <?php endif; ?>
                alt="<?php echo e(env('APP_NAME')); ?>" height="39">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <!-- Conditional Navigation Include -->
    <?php if( Request::is('settings*') ): ?>
        <?php $__env->startComponent('settings.components.nav'); ?>
        <?php echo $__env->renderComponent(); ?>
    <?php endif; ?>
    
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views\layouts\sidebar.blade.php ENDPATH**/ ?>