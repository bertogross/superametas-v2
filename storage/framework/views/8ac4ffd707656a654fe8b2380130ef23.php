<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Google Drive Files</title>
</head>
<body>
    <h1>Google Drive Files</h1>

    <form action="<?php echo e(route('googleDrive.upload')); ?>" method="post" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <input type="file" name="file" required>
        <button type="submit">Upload</button>
    </form>

    <ul>
        <?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li>
                <?php echo e($file->getName()); ?>

                <a href="<?php echo e(route('googleDrive.delete', $file->getId())); ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
</body>
</html>
<?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views/google-drive/list-files.blade.php ENDPATH**/ ?>