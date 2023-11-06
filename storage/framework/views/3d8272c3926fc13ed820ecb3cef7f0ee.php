<!doctype html>
<html class="no-focus" moznomarginboxes mozdisallowselectionprint lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" data-layout="horizontal" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-layout-mode="dark" data-layout-style="default" data-layout-width="fluid" data-layout-position="fixed" data-preloader="disable" data-bs-theme="dark">
    <head>
        <meta charset="utf-8" />
        <title><?php echo $__env->yieldContent('title'); ?> | <?php echo e(env('APP_NAME')); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="cache-control" content="no-cache">
        <meta http-equiv="pragma" content="no-cache">
        <meta http-equiv="expires" content="-1">
        <meta name="robots" content="noindex,nofollow,noarchive,nocache,nopreview,nosnippet,notranslate,noimageindex,nomediaindex,novideoindex,noodp,noydir">
        <meta content="Solution to help your team reach and exceed their sales goals" name="description" />
        <meta content="Supera Metas" name="author" />
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <meta name="robots" content="noindex, nofollow" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="<?php echo e(URL::asset('build/images/favicons/favicon.ico')); ?>">
            <?php echo $__env->make('layouts.head-css', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </head>

        <?php echo $__env->yieldContent('body'); ?>

        <?php echo $__env->yieldContent('content'); ?>

        <?php echo $__env->make('layouts.vendor-scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div id="custom-backdrop" class="d-none text-muted">
            <div style="display: flex; align-items: flex-end; justify-content: flex-start; height: 100vh; padding: 25px;">
                Para continuar trabalhando enquanto este processo está em andamento, <a href="<?php echo e(url('/')); ?>" target="_blank" class="text-theme me-1 ms-1">clique aqui</a> para abrir o <?php echo e(env('APP_NAME')); ?> em nova guia
            </div>
        </div>

        <div id="modalContainer"></div>

    </body>
</html>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/layouts/master-without-nav.blade.php ENDPATH**/ ?>