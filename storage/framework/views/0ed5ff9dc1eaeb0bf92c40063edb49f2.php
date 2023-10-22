<?php
// Initialize arrays to store the sum of sales and goals for each company
$totalSales = [];
$totalGoals = [];
$totalPercent = [];
$uniqueDepartments = [];
$metric = metricGoalSales($getMeantime);
$totalPercentAccrued = 0;
$ndxChartId = 0;
?>

<div id="load-listing" class="mb-4 rounded position-relative wrap-filter-result toogle_zoomInOut ribbon-box border ribbon-fill shadow-none">
    <div class="ribbon ribbon-info bg-theme text-black fs-12 <?php if(empty($data)): ?> d-none <?php endif; ?>" style="z-index: 2; scale: 1.5; top: -10px; left: -30px;">
        <?php echo e($metric . '%'); ?>

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
                            $ndxChartId++;

                            $sales = floatval($departments[$departmentId]['sales'] ?? 0);
                            $goal = floatval($departments[$departmentId]['goal'] ?? 0);

                            $percent = $sales > 0 && $goal > 0 ? ($sales / $goal) * 100 : 0;

                            $percentAccrued = ($percent/$metric) * 100;

                            // Calculate the sum of sales and goals for each company
                            $totalSales[$companyId] = floatval($totalSales[$companyId] ?? 0) + $sales;
                            $totalGoals[$companyId] = floatval($totalGoals[$companyId] ?? 0) + $goal;

                            $totalPercent[$companyId] = $totalSales[$companyId] > 1 && $totalGoals[$companyId] > 1 ? ($totalSales[$companyId] / $totalGoals[$companyId]) * 100 : 0;
                            ?>
                            <td class="text-center align-middle" data-company-id="<?php echo e($companyId); ?>" data-chart-id="<?php echo e($ndxChartId); ?>">
                                
                                <?php
                                echo goalsEmojiChart($ndxChartId, $goal, $sales, $departmentId, getDepartmentAlias($departmentId), getCompanyAlias($companyId), $percent, $percentAccrued)
                                ?>
                            </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <tr tr-department="sum" class="">
                    <th scope="row" class="text-uppercase fs-16 align-middle text-end p-3">
                        GERAL
                    </th>
                    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $companyId => $departments): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                        $ndxChartId++;

                        //APP_print_r($totalPercent);
                        $totalPercentValue = number_format($totalPercent[$companyId] ?? 0, 2, '.', '');
                        $totalPercentAccrued = ($totalPercentValue / $metric) * 100;
                        ?>
                        <td class="text-center align-middle" data-company-id="<?php echo e($companyId); ?>" data-chart-id="<?php echo e($ndxChartId); ?>">
                            
                            <?php
                            echo goalsEmojiChart($ndxChartId, number_format($totalGoals[$companyId] ?? 0, 2, '.', ''), number_format($totalSales[$companyId] ?? 0, 2, '.', ''), $departmentId, getDepartmentAlias($departmentId), getCompanyAlias($companyId), $totalPercentValue, $totalPercentAccrued, 'general')
                            ?>
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