<script src="<?php echo e(URL::asset('build/libs/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('build/libs/simplebar/simplebar.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('build/libs/node-waves/waves.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('build/libs/feather-icons/feather.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('build/js/pages/plugins/lord-icon-2.1.0.js')); ?>"></script>
<script src="<?php echo e(URL::asset('build/libs/choices.js/public/assets/scripts/choices.min.js')); ?>"></script>

<?php echo $__env->yieldContent('script'); ?>
<?php echo $__env->yieldContent('script-bottom'); ?>

<script>
// Prevent data-choices sort comapnies by name
var isChoiceEl = document.getElementById("filter-companies");
if(isChoiceEl){
    var choices = new Choices(isChoiceEl, {
        shouldSort: false,
        removeItems: true,
        removeItemButton: true
    });
}
</script>

<script>
window.App = <?php echo json_encode([
    'url' => URL::asset('/'),
]); ?>;
</script>
<script src="<?php echo e(URL::asset('build/js/app.js')); ?>"></script>
<?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views/layouts/vendor-scripts.blade.php ENDPATH**/ ?>