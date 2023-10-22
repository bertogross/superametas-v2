<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.lock-screen'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<div class="auth-page-wrapper pt-5">
    <!-- auth page bg -->
    <div class="auth-one-bg-position auth-one-bg"  id="auth-particles">
        <div class="bg-overlay"></div>

        <div class="shape">
            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1440 120">
                <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
            </svg>
        </div>
    </div>

    <!-- auth page content -->
    <div class="auth-page-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center mt-sm-5 mb-4 text-white-50">
                        <div>
                            <a href="<?php echo e(url('/')); ?>" class="d-inline-block auth-logo">
                                <img src="<?php echo e(URL::asset('build/images/logo-light.png')); ?>" alt="<?php echo e(env('APP_NAME')); ?>" height="39">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->

            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card mt-4">

                        <div class="card-body p-4">
                            <div class="text-center mt-2">
                                <h5 class="text-theme">Tela Bloqueada</h5>
                                <p class="text-muted">Para desbloquear informe sua senha!</p>
                            </div>
                            <div class="user-thumb text-center">
                                <img src="<?php if(Auth::user()->avatar != ''): ?><?php echo e(URL::asset('build/images/' . Auth::user()->avatar)); ?><?php else: ?><?php echo e(URL::asset('build/images/users/user-dummy-img.jpg')); ?><?php endif; ?>" class="rounded-circle img-thumbnail avatar-lg" alt="thumbnail">
                                <h5 class="font-size-15 mt-3"><?php echo e(Auth::user()->name); ?></h5>
                            </div>
                            <div class="p-2 mt-4">
                                <form>
                                    <div class="mb-3">
                                        <label class="form-label" for="userpassword">Senha</label>
                                        <input type="password" class="form-control" id="userpassword" placeholder="Senha aqui" required>
                                    </div>
                                    <div class="mb-2 mt-4">
                                        <button class="btn btn-theme w-100" type="submit">Desbloquear</button>
                                    </div>
                                </form><!-- end form -->

                            </div>
                        </div>
                        <!-- end card body -->
                    </div>
                    <!-- end card -->

                    <div class="mt-4 text-center">
                        <p class="mb-0">Não é você? <a href="javascript:void();" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="fw-semibold text-theme text-decoration-underline"> Sair </a>
                            <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                                <?php echo csrf_field(); ?>
                            </form>
                        </p>
                    </div>

                </div>
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <!-- end auth page content -->

    <!-- footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center">
                        <p class="mb-0 text-muted">&copy; <script>document.write(new Date().getFullYear())</script> Supera Metas</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- end Footer -->
</div>
<!-- end auth-page-wrapper -->
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('build/libs/particles.js/particles.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/js/pages/particles.app.js')); ?>"></script>
<?php $__env->stopSection(); ?>

</html>

<?php echo $__env->make('layouts.master-without-nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views\_many_other_templates\auth-lockscreen-basic.blade.php ENDPATH**/ ?>