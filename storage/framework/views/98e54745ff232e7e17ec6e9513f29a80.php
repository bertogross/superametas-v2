<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18"><?php echo e($title); ?></h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <?php if(isset($li_1)): ?>
                        <li class="breadcrumb-item">
                            <a href="<?php if(isset($url)): ?><?php echo e($url); ?><?php else: ?> javascript: void(0);<?php endif; ?>">
                                <?php echo e($li_1); ?>

                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if(isset($title) && isset($li_1)): ?>
                        <li class="breadcrumb-item active"><?php echo e($title); ?></li>
                    <?php endif; ?>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->
<?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views/components/breadcrumb.blade.php ENDPATH**/ ?>