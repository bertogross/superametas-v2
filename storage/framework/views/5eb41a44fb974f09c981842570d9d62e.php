<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.error'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('body'); ?>
<body>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <!-- auth-page wrapper -->
    <div class="auth-page-wrapper py-5 d-flex justify-content-center align-items-center min-vh-100">

        <!-- auth-page content -->
        <div class="auth-page-content overflow-hidden p-0">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-xl-4 text-center">
                        <div class="error-500 position-relative">
                            <img src="<?php echo e(URL::asset('build/images/error500.png')); ?>" alt="" class="img-fluid error-500-img error-img" />
                            <h1 class="title text-muted"></h1>
                        </div>
                        <div>
                            <h4>Error!</h4>
                            <p class="text-muted w-75 mx-auto">Não sabemos exatamente o que aconteceu, mas nossos servidores dizem que algo está errado.</p>
                            <a href="<?php echo e(url('/')); ?>" class="btn btn-success"><i class="mdi mdi-home me-1"></i>Voltar ao início</a>
                        </div>
                    </div><!-- end col-->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth-page content -->
    </div>
    <!-- end auth-page-wrapper -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master-without-nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views\error\general.blade.php ENDPATH**/ ?>