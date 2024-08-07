<!-- ========== App Menu ========== -->
<?php
    $getCompanyLogo = getCompanyLogo();

    $host = $_SERVER['HTTP_HOST'] ?? 'default';
    $logo2 = str_contains($host, 'testing') ? '-2' : '';
?>
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="<?php echo e(url('/')); ?>" class="logo logo-dark" title="Ir para inicial do <?php echo e(appName()); ?>">
            <span class="logo-sm">
                <img src="<?php echo e(URL::asset('build/images/logo-sm' . $logo2 . '.png')); ?>" alt="<?php echo e(appName()); ?>" height="22">
            </span>
            <span class="logo-lg">
                <img
                <?php if($getCompanyLogo): ?>
                    src="<?php echo e($getCompanyLogo); ?>"
                <?php else: ?>
                    src="<?php echo e(URL::asset('build/images/logo-dark' . $logo2 . '.png')); ?>"
                <?php endif; ?>
                alt="<?php echo e(appName()); ?>" height="31" loading="lazy">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="<?php echo e(url('/')); ?>" class="logo logo-light" title="Ir para inicial do <?php echo e(appName()); ?>">
            <span class="logo-sm">
                <img src="<?php echo e(URL::asset('build/images/logo-sm' . $logo2 . '.png')); ?>" alt="<?php echo e(appName()); ?>" height="22">
            </span>
            <span class="logo-lg">
                <img
                <?php if($getCompanyLogo): ?>
                    src="<?php echo e($getCompanyLogo); ?>"
                <?php else: ?>
                    src="<?php echo e(URL::asset('build/images/logo-light' . $logo2 . '.png')); ?>"
                <?php endif; ?>
                alt="<?php echo e(appName()); ?>" height="31" loading="lazy">
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
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>