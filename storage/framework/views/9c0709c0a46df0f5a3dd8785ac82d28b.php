<div id="load-listing" class="mb-4 rounded position-relative toogle_zoomInOut ribbon-box border ribbon-fill shadow-none <?php if(!$data): ?> d-none <?php endif; ?>">
    <div class="ribbon ribbon-info bg-theme text-black fs-12 <?php if(empty($data)): ?> d-none <?php endif; ?>" style="z-index: 2; scale: 1.5; top: -10px; left: -30px;">
        <?php echo e($metric . '%'); ?>

    </div>

    <div class="row listing-chart">
        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $companyId => $departments): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $departmentId => $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $ndxChartId++;

                    $sales = floatval($values['sales'] ?? 0);
                    $goal = floatval($values['goal'] ?? 0);

                    $percent = $sales > 0 && $goal > 0 ? ($sales / $goal) * 100 : 0;

                    $percentAccrued = $percent > 0 && $metric > 0 ? ($percent/$metric) * 100 : 0;

                    // Calculate the sum of sales and goals for each company
                    $totalSales[$companyId] = floatval($totalSales[$companyId] ?? 0) + $sales;
                    $totalGoals[$companyId] = floatval($totalGoals[$companyId] ?? 0) + $goal;

                    $totalPercent[$companyId] = $totalSales[$companyId] > 1 && $totalGoals[$companyId] > 1 ? ($totalSales[$companyId] / $totalGoals[$companyId]) * 100 : 0;
                ?>
                <div class="col m-4 text-center text-uppercase">

                    <?php
                        echo goalsEmojiChart($ndxChartId, $goal, $sales, $departmentId, getDepartmentAlias($departmentId), getCompanyAlias($companyId), $percent, $percentAccrued);
                    ?>
                    <div class="chart-label fw-bold"><?php echo e(getDepartmentAlias($departmentId)); ?></div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php if(count($getActiveDepartments) > 1): ?>
                <div class="col-12 m-4 text-center text-uppercase <?php if(!empty($filterDepartments) && count($filterDepartments) == 1): ?> d-none <?php endif; ?>">

                    <?php
                    $ndxChartId++;

                    //APP_print_r($totalPercent);
                    $totalPercentValue = number_format($totalPercent[$companyId] ?? 0, 2, '.', '');
                    $totalPercentAccrued = ($totalPercentValue / $metric) * 100;

                    echo goalsEmojiChart($ndxChartId, number_format($totalGoals[$companyId] ?? 0, 2, '.', ''), number_format($totalSales[$companyId] ?? 0, 2, '.', ''), 'general', 'Geral', getCompanyAlias($companyId), $totalPercentValue, $totalPercentAccrued, 'general');
                    ?>
                    <div class="chart-label fw-bold fs-4">Geral</div>
                </div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views/goal-sales/single.blade.php ENDPATH**/ ?>
