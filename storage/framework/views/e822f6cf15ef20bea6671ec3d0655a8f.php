<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" data-layout="vertical" data-layout-style="default" data-layout-position="fixed" data-topbar="light" data-sidebar="dark" data-sidebar-size="sm-hover" data-layout-width="fluid">
    <head>
        <meta charset="utf-8" />
        <title><?php echo $__env->yieldContent('title'); ?> | <?php echo e(env('APP_NAME')); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="cache-control" content="no-cache">
        <meta http-equiv="pragma" content="no-cache">
        <meta http-equiv="expires" content="-1">
        <meta name="robots" content="noindex,nofollow,nopreview,nosnippet,notranslate,noimageindex,nomediaindex,novideoindex,noodp,noydir">
        <meta property="og:image" content="<?php echo e(URL::asset('build/images/logo-sm.png')); ?>">
        <meta content="Solução para ajudar sua equipe a atingir e Superar suas Metas de Vendas" name="description" />
        <meta name="author" content="<?php echo e(env('APP_NAME')); ?>" />
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <!-- App favicon -->
        <link rel="icon" type="image/png" href="<?php echo e(URL::asset('build/images/logo-sm.png')); ?>">
        <link rel="shortcut icon" href="<?php echo e(URL::asset('build/images/favicons/favicon.ico')); ?>">
        <?php echo $__env->make('layouts.head-css', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </head>
    <body>
        <script>0</script>
        <!-- Begin page -->
        <div id="layout-wrapper">

            <?php echo $__env->make('layouts.topbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo $__env->make('layouts.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">
                <div class="page-content">
                    <!-- Start content -->
                    <div class="container-fluid">
                        <?php echo $__env->yieldContent('content'); ?>
                    </div> <!-- content -->
                </div>
                <?php echo $__env->make('layouts.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <!-- ============================================================== -->
            <!-- End Right content here -->
            <!-- ============================================================== -->
        </div>
        <!-- END wrapper -->

        <?php echo $__env->make('layouts.vendor-scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div id="custom-backdrop" class="d-none text-white">
            <div style="display: flex; align-items: flex-end; justify-content: flex-start; height: 100vh; padding: 25px; padding-bottom: 70px;">
                Para continuar trabalhando enquanto este processo está em andamento, <a href="<?php echo e(url('/')); ?>" target="_blank" class="text-theme me-1 ms-1">clique aqui</a> para abrir o <?php echo e(env('APP_NAME')); ?> em nova guia
            </div>
        </div>

        <div id="modalContainer"></div>
    </body>
</html>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views\layouts\layouts-vertical-hovered.blade.php ENDPATH**/ ?>