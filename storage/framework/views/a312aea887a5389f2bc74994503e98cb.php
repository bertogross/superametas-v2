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

<?php
    $HTTP_HOST = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    $SUBDOMAIN = $HTTP_HOST ? strtok($HTTP_HOST, '.') : '';
?>
<?php if( $SUBDOMAIN && $SUBDOMAIN != 'app' ): ?>
    <div class="ribbon-box border-0 ribbon-fill position-fixed top-0 start-0 d-none d-lg-block d-xl-block" data-bs-toggle="tooltip" data-bs-placement="right" title="<?php echo e($SUBDOMAIN); ?> Environment" style="z-index:5000; width: 60px; height:60px;">
        <div class="ribbon ribbon-<?php echo e($SUBDOMAIN == 'development' ? 'danger' : 'warning'); ?> text-uppercase fs-10"><?php echo e(str_replace(['localhost:8000', 'localhost'], 'local', $SUBDOMAIN)); ?></div>
    </div>
<?php endif; ?>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/layouts/vendor-scripts.blade.php ENDPATH**/ ?>