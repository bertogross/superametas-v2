<?php
// Initialize arrays to store the sum of sales and goals for each company
$totalSales = [];
$totalGoals = [];
$uniqueDepartments = [];
?>

<div id="load-listing" class="mb-4 rounded position-relative wrap-filter-result toogle_zoomInOut ribbon-box border ribbon-fill shadow-none">
    <div class="ribbon ribbon-info bg-theme text-black fs-12 <?php if(empty($data)): ?> d-none <?php endif; ?>" style="z-index: 2; scale: 1.5; top: -10px; left: -30px;">
        <?php echo e(metricGoalSales()); ?>

    </div>
    <div class="table-responsive mb-0">
        <table id="goal-sales-dataTable" class="table table-striped-columns table-nowrap listing-chart mb-0">
            <thead class="text-uppercase table-light">
                <tr>
                    <th scope="col" class="bg-transparent fs-20 text-center invisible"></th>
                    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $companyId => $departments): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th scope="col" class="text-center" data-company-id="<?php echo e($companyId); ?>">
                            <?php echo e(getCompanyAlias(intval($companyId))); ?>

                        </th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $companyId => $departments): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $departmentId => $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                        $uniqueDepartments[$departmentId] = getDepartmentAlias(intval($departmentId));
                        ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php $__currentLoopData = $uniqueDepartments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $departmentId => $departmentAlias): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr tr-department="<?php echo e($departmentId); ?>" class="">
                        <th scope="row" class="text-uppercase fs-16 align-middle text-end p-3">
                            <?php echo e($departmentAlias); ?>

                        </th>
                        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $companyId => $departments): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                            $sales = $departments[$departmentId]['sales'] ?? 0;
                            $goal = $departments[$departmentId]['goal'] ?? 0;

                            // Calculate the sum of sales and goals for each company
                            $totalSales[$companyId] = ($totalSales[$companyId] ?? 0) + $sales;
                            $totalGoals[$companyId] = ($totalGoals[$companyId] ?? 0) + $goal;
                            ?>

                            <td class="text-center align-middle" data-company-id="<?php echo e($companyId); ?>" data-chart-id="0">
                                <div>Sales: <?php echo e(number_format($sales, 2, '.', '')); ?></div>
                                <div>Goals: <?php echo e(number_format($goal, 2, '.', '')); ?></div>
                            </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <tr tr-department="sum" class="">
                    <th scope="row" class="text-uppercase fs-16 align-middle text-end p-3">
                        GERAL
                    </th>
                    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $companyId => $departments): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <td class="text-center align-middle" data-company-id="<?php echo e($companyId); ?>" data-chart-id="0">
                            <div>Sales: <?php echo e(number_format($totalSales[$companyId] ?? 0, 2, '.', '')); ?></div>
                            <div>Goals: <?php echo e(number_format($totalGoals[$companyId] ?? 0, 2, '.', '')); ?></div>
                        </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
            </tbody>
            <tfoot class="text-uppercase table-light">
                <tr>
                    <th scope="col" class="bg-transparent invisible"></th>
                    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $companyId => $departments): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th scope="col" class="text-center" data-company-id="<?php echo e($companyId); ?>">
                            <?php echo e(getCompanyAlias(intval($companyId))); ?>

                        </th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views/goal-sales/table.blade.php ENDPATH**/ ?>