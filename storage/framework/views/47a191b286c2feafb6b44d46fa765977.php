<?php $__env->startSection('title'); ?>
    Meu <?php echo e(env('APP_NAME')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(URL::asset('build/libs/dropzone/dropzone.css')); ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(URL::asset('build/libs/filepond/filepond.min.css')); ?>" type="text/css" />
    <link rel="stylesheet" href="<?php echo e(URL::asset('build/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css')); ?>">
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
            Meu <?php echo e(env('APP_NAME')); ?>

        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <?php echo $__env->make('error.alert-errors', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->make('error.alert-success', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- resources/views/settings/database.blade.php -->

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-2">
                    <div class="nav nav-pills flex-column nav-pills-tab verti-nav-pills custom-verti-nav-pills nav-pills-theme" role="tablist" aria-orientation="vertical">
                        <a class="nav-link text-uppercase <?php echo e(session('active_tab') == 'account' || session('active_tab') == '' ? 'active show' : ''); ?>" id="v-pills-account-tab" data-bs-toggle="pill" href="#v-pills-account" role="tab" aria-controls="v-pills-account"
                            aria-selected="true">
                            Minha Conta</a>
                        <a class="nav-link text-uppercase <?php echo e(session('active_tab') == 'stripe' ? 'active show' : ''); ?>" id="v-pills-stripe-tab" data-bs-toggle="pill" href="#v-pills-stripe" role="tab" aria-controls="v-pills-stripe"
                            aria-selected="false">
                            Faturamento</a>
                    </div>
                </div> <!-- end col-->
                <div class="col-lg-10">
                    <div class="tab-content text-muted mt-3 mt-lg-0">
                        <div class="tab-pane fade <?php echo e(session('active_tab') == 'account' || session('active_tab') == '' ? 'active show' : ''); ?>" id="v-pills-account" role="tabpanel" aria-labelledby="v-pills-account-tab">
                            <form action="<?php echo e(route('settingsAccountStoreURL')); ?>" method="POST" enctype="multipart/form-data" autocomplete="off">
                                <?php echo csrf_field(); ?>
                                <div class="mb-3">
                                    <label class="form-label" for="name">Nome da Empresa:</label>
                                    <input type="text" name="name" id="name" class="form-control" maxlength="190" value="<?php echo e(old('name', $settings['name'] ?? '')); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="phone">Número do telefone móvel:</label>
                                    <input type="tel" name="phone" id="phone" class="form-control phone-mask" value="<?php echo e(old('phone', formatPhoneNumber($settings['phone']) ?? '')); ?>" maxlength="16" required>
                                </div>

                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h4 class="card-title mb-0">Envie o logotipo de sua empresa</h4>
                                    </div>
                                    <div class="card-body">
                                        <p class="form-text">
                                            Formato suportado: <strong class="text-theme">JPG</strong> | Dimensão recomendada: 200 x 200 pixels
                                        </p>
                                        <div class="avatar-xl mx-auto">
                                            <input
                                            type="file"
                                            class="filepond filepond-input-logo"
                                            name="logo"
                                            <?php if(isset($settings['logo']) && $settings['logo']): ?>
                                                data-default-file="<?php echo e(asset('storage/' . $settings['logo'])); ?>"
                                            <?php endif; ?>
                                            accept="image/jpeg"/>
                                        </div>
                                    </div>
                                </div>

                                <input type="submit" class="btn btn-theme" value="Atualizar Minha Conta">
                            </form>
                        </div><!--end tab-pane-->

                        <div class="tab-pane fade <?php echo e(session('active_tab') == 'stripe' ? 'active show' : ''); ?>" id="v-pills-stripe" role="tabpanel" aria-labelledby="v-pills-stripe-tab">
                            STRIPE HERE
                        </div><!--end tab-pane-->
                    </div>
                </div> <!-- end col-->
            </div> <!-- end row-->
        </div><!-- end card-body -->
    </div><!--end card-->

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('build/js/pages/password-addon.init.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('build/libs/dropzone/dropzone-min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/filepond/filepond.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/filepond-plugin-file-encode/filepond-plugin-file-encode.min.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('build/js/settings-account.js')); ?>" type="module"></script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views/settings/account.blade.php ENDPATH**/ ?>