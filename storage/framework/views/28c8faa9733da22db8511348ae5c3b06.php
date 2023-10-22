<?php $__env->startSection('title'); ?>
<?php echo app('translator')->get('translation.signup'); ?>
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
                                        <h5 class="text-primary">Create New Account</h5>
                                        <p class="text-muted">Get your free velzon account now</p>
                                    </div>
                                    <div class="p-2 mt-4">
                                        <form class="needs-validation" novalidate action="index">

                                            <div class="mb-3">
                                                <label for="useremail" class="form-label">E-mail <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="useremail" placeholder="Enter email address" required>
                                                <div class="invalid-feedback">
                                                    Please enter email
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="username" placeholder="Enter username" required>
                                                <div class="invalid-feedback">
                                                    Please enter username
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label" for="password-input">Password</label>
                                                <div class="position-relative auth-pass-inputgroup">
                                                    <input type="password" class="form-control pe-5 password-input" onpaste="return false" placeholder="Senha aqui" id="password-input" aria-describedby="passwordInput" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" maxlength="20" minlength="8" required>
                                                    <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle text-body"></i></button>
                                                    <div class="invalid-feedback">
                                                        Informe a senha
                                                    </div>
                                                    <div id="passwordHelpBlock" class="form-text">
                                                        Sua senha deve conter entre 8 a 20 caracteres, letras e números e não deve ter espaços, caracteres especiais ou emojis.
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <p class="mb-0 fs-12 text-muted fst-italic">Ao cadastrar-se, você concorda com os <a href="#" class="text-primary text-decoration-underline fst-normal fw-medium">Termos de Uso</a></p>
                                            </div>

                                            <div id="password-contain" class="p-3 bg-light mb-2 rounded">
                                                <h5 class="fs-13">Password must contain:</h5>
                                                <p id="pass-length" class="invalid fs-12 mb-2">Minimum <b>8 characters</b></p>
                                                <p id="pass-lower" class="invalid fs-12 mb-2">At <b>lowercase</b> letter (a-z)</p>
                                                <p id="pass-upper" class="invalid fs-12 mb-2">At least <b>uppercase</b> letter (A-Z)</p>
                                                <p id="pass-number" class="invalid fs-12 mb-0">A least <b>number</b> (0-9)</p>
                                            </div>

                                            <div class="mt-4">
                                                <button class="btn btn-success w-100" type="submit">Sign Up</button>
                                            </div>

                                        </form>

                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->

                            <div class="mt-4 text-center">
                                <p class="mb-0">Já possui cadastro? <a href="auth-signin-basic" class="fw-semibold text-theme text-decoration-underline"> Clique aqui </a> </p>
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
        <?php $__env->stopSection(); ?>
        <?php $__env->startSection('script'); ?>
            <script src="<?php echo e(URL::asset('build/libs/particles.js/particles.js')); ?>"></script>
            <script src="<?php echo e(URL::asset('build/js/pages/particles.app.js')); ?>"></script>
            <script src="<?php echo e(URL::asset('build/js/pages/form-validation.init.js')); ?>"></script>
            <script src="<?php echo e(URL::asset('build/js/pages/passowrd-create.init.js')); ?>"></script>
        <?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.master-without-nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views\_many_other_templates\auth-signup-basic.blade.php ENDPATH**/ ?>