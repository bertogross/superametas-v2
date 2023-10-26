<div class=" mb-4 rounded position-relative toogle_zoomInOut ribbon-box border ribbon-fill shadow-none" id="load-listing">
    <div class="ribbon ribbon-info bg-theme text-black fs-12 <?php if(empty($result) ): ?> d-none <?php endif; ?>" style="z-index: 2; scale: 1.5; top: -10px; left: -30px;">
        <?php
            echo metricGoalSales();
        ?>
    </div>
    <div class="table-responsive mb-0">
        <table id="goal-sales-dataTable" class="table table-striped-columns table-nowrap listing-chart mb-0">
            <thead class="text-uppercase table-light">
                <tr>
                    <th scope="col" class="bg-transparent fs-20 text-center invisible"></th>
                    <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th scope="col" class="text-center" data-company-id="<?php echo e($company); ?>">
                            <?php echo e(getCompanyAlias(intval($company))); ?>

                        </th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $result; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr tr-department="<?php echo e($row['department_id']); ?>" class="">
                        <th scope="row" class="text-uppercase fs-16 align-middle text-end p-3">
                            <?php echo e(getDepartmentAlias(intval($row['department_id']))); ?>

                        </th>
                        <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <td class="text-center align-middle" data-company-id="<?php echo e($company); ?>" data-chart-id="0">
                                <?php echo e($row[$company]); ?>

                            </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
            <tfoot class="text-uppercase table-light">
                <tr>
                    <th scope="col" class="bg-transparent invisible"></th>
                    <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th scope="col" class="text-center" data-company-id="<?php echo e($company); ?>">
                            <?php echo e(getCompanyAlias(intval($company))); ?>

                        </th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views/goal-sales-table.blade.php ENDPATH**/ ?>
