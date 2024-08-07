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
                    <?php echo e(appName()); ?> - <?php echo e(date('Y')); ?> ©
                <?php else: ?>
                    <?php echo e(date('Y')); ?> © <?php echo e(appName()); ?>

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
<?php /**PATH /var/www/html/development.superametas.com/public_html/resources/views/layouts/footer.blade.php ENDPATH**/ ?>