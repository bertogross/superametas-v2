<?php
    $getCompanyLogo = getCompanyLogo();
?>
<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <?php if($getCompanyLogo): ?>
                    <a href="<?php echo e(url('/')); ?>" title="Ir para inicial do <?php echo e(env('APP_NAME')); ?>">
                        <img src="<?php echo e(URL::asset('build/images/logo-light.png')); ?>" alt="<?php echo e(env('APP_NAME')); ?>" height="27">
                    </a>
                <?php else: ?>
                    <script>document.write(new Date().getFullYear())</script> © <?php echo e(env('APP_NAME')); ?>

                <?php endif; ?>
            </div>
            <div class="col-sm-6">
                <div class="text-sm-end d-none d-sm-block">
                    <span class="badge bg-warning">Badge</span>
                </div>
            </div>
        </div>
    </div>
</footer>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views\layouts\footer.blade.php ENDPATH**/ ?>