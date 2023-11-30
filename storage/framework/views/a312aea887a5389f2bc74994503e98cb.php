<script src="<?php echo e(URL::asset('build/libs/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('build/libs/simplebar/simplebar.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('build/libs/node-waves/waves.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('build/libs/feather-icons/feather.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('build/js/pages/plugins/lord-icon-2.1.0.js')); ?>"></script>
<script src="<?php echo e(URL::asset('build/libs/sweetalert2/sweetalert2.min.js')); ?>"></script>

<?php echo $__env->yieldContent('script'); ?>
<?php echo $__env->yieldContent('script-bottom'); ?>

<script>
window.App = <?php echo json_encode([
    'url' => URL::asset('/'),
]); ?>;
</script>
<script src="<?php echo e(URL::asset('build/js/app.js')); ?>"></script>

<script>
    var profileChangeLayoutModeURL = "<?php echo e(route('profileChangeLayoutModeURL')); ?>";
</script>
<script src="<?php echo e(URL::asset('build/js/app-custom.js')); ?>" type="module"></script>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/layouts/vendor-scripts.blade.php ENDPATH**/ ?>