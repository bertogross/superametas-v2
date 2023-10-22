<?php if($errors->any()): ?>
    <div class="alert alert-danger mb-0">
        <ul class="list-unstyled">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><i class="ri-close-fill align-bottom me-1"></i><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>
<?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views/error/alert-errors.blade.php ENDPATH**/ ?>