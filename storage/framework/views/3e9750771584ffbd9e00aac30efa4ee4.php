<?php
    $getCompanyLogo = getCompanyLogo();
?>
<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <?php if($getCompanyLogo): ?>
                    <a href="<?php echo e(url('/')); ?>" title="Ir para inicial do <?php echo e(appName()); ?>">
                        <img src="<?php echo e(URL::asset('build/images/logo-sm.png')); ?>" alt="<?php echo e(appName()); ?>" class="me-1" height="27" loading="lazy">
                    </a>
                    <?php echo e(appName()); ?> - <script>document.write(new Date().getFullYear())</script> ©
                <?php else: ?>
                    <script>document.write(new Date().getFullYear())</script> © <?php echo e(appName()); ?>

                <?php endif; ?>
            </div>
            <div class="col-sm-6">
                <div class="text-sm-end d-none d-sm-block">
                    <?php echo e(subscriptionLabel()); ?>

                </div>
            </div>
        </div>
    </div>
</footer>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/layouts/footer.blade.php ENDPATH**/ ?>