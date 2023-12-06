<?php if(session('success')): ?>
    <!-- Success Alert -->
    <div id="success-alert" class="alert alert-success alert-dismissible alert-label-icon label-arrow fade show" role="alert">
        <i class="ri-check-double-line label-icon"></i><?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss=" alert" aria-label="Close"></button>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Check if the element exists
            var alert = document.getElementById('success-alert');
            if (alert) {
                // Set a timeout to hide the alert after 10 seconds
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 30000);
            }
        });
    </script>
<?php endif; ?>


<?php if(session('warning')): ?>
    <div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show" role="alert">
        <i class="ri-alert-line label-icon"></i> <?php echo e(session('warning')); ?>

        <button type="button" class="btn-close" data-bs-dismiss=" alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show" role="alert">
        <i class="ri-error-warning-fill label-icon"></i> <?php echo e(session('error')); ?>

        <button type="button" class="btn-close" data-bs-dismiss=" alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <ul class="list-unstyled">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><i class="ri-close-fill align-bottom me-1"></i><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/components/alerts.blade.php ENDPATH**/ ?>