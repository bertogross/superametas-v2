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

    <?php echo $__env->make('components.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- resources/views/settings/database.blade.php -->

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-2">
                    <div class="nav nav-pills flex-column nav-pills-tab verti-nav-pills custom-verti-nav-pills nav-pills-theme" role="tablist" aria-orientation="vertical">
                        <a class="nav-link text-uppercase" href="#v-pills-account" id="v-pills-account-tab" data-bs-toggle="pill" role="tab" aria-controls="v-pills-account" aria-selected="true">Dados da Conta</a>

                        <a class="nav-link text-uppercase" id="v-pills-invoices-tab" data-bs-toggle="pill" href="#v-pills-invoices" role="tab" aria-controls="v-pills-invoices" aria-selected="false">Faturamento</a>

                        <a class="nav-link text-uppercase active show" id="v-pills-stripe-tab" data-bs-toggle="pill" href="#v-pills-stripe" role="tab" aria-controls="v-pills-stripe" aria-selected="false">Assinatura</a>
                    </div>
                </div> <!-- end col-->
                <div class="col-lg-10">
                    <div class="tab-content text-muted mt-3 mt-lg-2">
                        <div class="tab-pane fade" id="v-pills-account" role="tabpanel" aria-labelledby="v-pills-account-tab">
                            <?php echo $__env->make('settings.account-form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div><!--end tab-pane-->

                        <div class="tab-pane fade" id="v-pills-invoices" role="tabpanel" aria-labelledby="v-pills-invoices-tab">
                            <?php echo $__env->make('settings.stripe.invoices', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div><!--end tab-pane-->

                        <div class="tab-pane fade active show" id="v-pills-stripe" role="tabpanel" aria-labelledby="v-pills-stripe-tab">
                            <?php echo $__env->make('settings.stripe.subscription', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div><!--end tab-pane-->
                    </div>
                </div> <!-- end col-->
            </div> <!-- end row-->
        </div><!-- end card-body -->
    </div><!--end card-->

    <?php echo $__env->make('settings.stripe.modal-subscription-details', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->make('settings.stripe.modal-upcoming', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('build/js/pages/password-addon.init.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('build/libs/dropzone/dropzone-min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/filepond/filepond.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/filepond-plugin-file-encode/filepond-plugin-file-encode.min.js')); ?>"></script>

    <script>
        var uploadLogoURL = "<?php echo e(route('uploadLogoURL')); ?>";
    </script>
    <script src="<?php echo e(URL::asset('build/js/settings-account.js')); ?>" type="module"></script>

    <script>
        var stripeSubscriptionURL = "<?php echo e(route('stripeSubscriptionURL')); ?>";
        var stripeSubscriptionDetailsURL = "<?php echo e(route('stripeSubscriptionDetailsURL')); ?>";
        var stripeCartAddonURL = "<?php echo e(route('stripeCartAddonURL')); ?>";

        var assetUrl = "<?php echo e(URL::asset('/')); ?>";
    </script>
    <script src="<?php echo e(URL::asset('build/js/settings-stripe.js')); ?>" type="module"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views\settings\account.blade.php ENDPATH**/ ?>