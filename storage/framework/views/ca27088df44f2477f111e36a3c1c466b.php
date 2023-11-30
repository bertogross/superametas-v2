<div class="text-center m-4">
    <?php if(isset($_REQUEST['filter'])): ?>
        <h2 class="text-uppercase">&#129488;</h2>
        <p class="text-muted mb-4">Os parâmetros desta pesquisa não retornaram dados!</p>
    <?php else: ?>
        <h5 class="text-uppercase">Ainda não há dados 😭</h5>
        <p class="text-muted mb-4">Você deverá registrar informações!</p>
        <?php if(isset($url)): ?>
            <a class="btn btn-outline-theme" href="<?php echo e($url); ?>"><i class="ri-add-line"></i></a>
        <?php endif; ?>
        <?php if(isset($warning)): ?>
            <div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show" role="alert">
                <i class="ri-alert-line label-icon"></i> <?php echo $warning; ?>

            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views\components\nothing.blade.php ENDPATH**/ ?>