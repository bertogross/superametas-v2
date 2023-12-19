<?php
$host = $_SERVER['HTTP_HOST'] ?? 'default';
$logo2 = str_contains($host, 'testing') ? '-2' : '';
?>
<!doctype html>
<html class="no-focus" moznomarginboxes mozdisallowselectionprint lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" data-layout="horizontal" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-layout-mode="dark" data-layout-style="default" data-layout-width="fluid" data-layout-position="fixed" data-preloader="enable" data-bs-theme="dark">
    <head>
        <meta charset="utf-8" />
        <title><?php echo $__env->yieldContent('title'); ?> | <?php echo e(appName()); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="cache-control" content="no-cache">
        <meta http-equiv="pragma" content="no-cache">
        <meta http-equiv="expires" content="-1">
        <meta name="robots" content="noindex,nofollow,nopreview,nosnippet,notranslate,noimageindex,nomediaindex,novideoindex,noodp,noydir">
        <meta content="<?php echo e(appDescription()); ?>" name="description" />
        <meta property="og:image" content="<?php echo e(URL::asset('build/images/logo-sm' . $logo2 . '.png')); ?>">
        <meta name="author" content="<?php echo e(appName()); ?>" />
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <!-- App favicon -->
        <link rel="icon" type="image/png" href="<?php echo e(URL::asset('build/images/logo-sm' . $logo2 . '.png')); ?>">
        <link rel="shortcut icon" href="<?php echo e(URL::asset('build/images/favicons/favicon' . $logo2 . '.ico')); ?>">
            <?php echo $__env->make('layouts.head-css', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </head>

        <?php echo $__env->yieldContent('body'); ?>

        <!--preloader-->
        <div id="preloader">
            <div id="status">
                <div class="spinner-border text-theme avatar-sm" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>

        <?php echo $__env->yieldContent('content'); ?>

        <?php echo $__env->make('layouts.vendor-scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div id="custom-backdrop" class="d-none text-white">
            <div style="display: flex; align-items: flex-end; justify-content: flex-start; height: 100vh; padding: 25px; padding-bottom: 70px;">
                Para continuar trabalhando enquanto este processo está em andamento, <a href="<?php echo e(url('/')); ?>" target="_blank" class="text-theme me-1 ms-1">clique aqui</a> para abrir o <?php echo e(appName()); ?> em nova guia
            </div>
        </div>

        <div id="modalContainer"></div>

    </body>
</html>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/layouts/master-without-nav.blade.php ENDPATH**/ ?>