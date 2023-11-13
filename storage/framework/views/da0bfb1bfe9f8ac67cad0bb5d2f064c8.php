<div id="load-listing" class="mb-4 rounded position-relative toogle_zoomInOut ribbon-box border ribbon-fill shadow-none bg-light bg-opacity-25 <?php if(!$data): ?> d-none <?php endif; ?>">
    <div class="ribbon ribbon-info bg-theme text-black fs-12 <?php if(empty($data)): ?> d-none <?php endif; ?>" style="z-index: 2; scale: 1.5; top: -10px; left: -30px;">
        <!-- Ensure that $metric is a number before appending the '%' sign -->
        <?php echo e($metric . '%'); ?>

    </div>

    <div class="row listing-chart">
        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $companyId => $departments): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $departmentId => $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $ndxChartId++;

                    // Convert sales and goal to float values to ensure they are numbers
                    $sales = floatval($values['sales'] ?? 0);
                    $goal = floatval($values['goal'] ?? 0);

                    // Calculate percentage, ensuring not to divide by zero
                    $percent = $goal > 0 ? ($sales / $goal) * 100 : 0;

                    // Calculate accrued percent, ensuring not to divide by zero and that $metric is numeric
                    $percentAccrued = ($percent > 0 && is_numeric($metric) && $metric > 0) ? ($percent / $metric) * 100 : 0;

                    // Initialize total sales and goals for each company if not already set
                    $totalSales[$companyId] = $totalSales[$companyId] ?? 0;
                    $totalGoals[$companyId] = $totalGoals[$companyId] ?? 0;

                    // Add current sales and goals to the total
                    $totalSales[$companyId] += $sales;
                    $totalGoals[$companyId] += $goal;

                    // Calculate total percent, ensuring not to divide by zero
                    $totalPercent[$companyId] = $totalGoals[$companyId] > 0 ? ($totalSales[$companyId] / $totalGoals[$companyId]) * 100 : 0;
                ?>
                <div class="col-sm-6 col-md-4 col-lg-3 col-xxl-2 m-4 text-center text-uppercase">
                    <!-- Removed the commented-out code for clarity -->
                    <?php
                        // Use number_format to format the numbers for display only, not for calculations
                        echo goalsEmojiChart($ndxChartId, $goal, $sales, $departmentId, getDepartmentAlias($departmentId), getCompanyAlias($companyId), number_format($percent, 2), number_format($percentAccrued, 2));
                    ?>
                    <div class="chart-label fw-bold"><?php echo e(getDepartmentAlias($departmentId)); ?></div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php if(count($getActiveDepartments) > 1): ?>
                <div class="col-12 m-4 text-center text-uppercase <?php if(!empty($filterDepartments) && count($filterDepartments) == 1): ?> d-none <?php endif; ?>">
                    <?php
                    $ndxChartId++;

                    // Calculate total percent value and accrued percent, ensuring not to divide by zero and that $metric is numeric
                    $totalPercentValue = $totalGoals[$companyId] > 0 ? ($totalSales[$companyId] / $totalGoals[$companyId]) * 100 : 0;
                    $totalPercentAccrued = (is_numeric($metric) && $metric > 0) ? ($totalPercentValue / $metric) * 100 : 0;

                    // Use number_format to format the numbers for display only, not for calculations
                    echo goalsEmojiChart($ndxChartId, number_format($totalGoals[$companyId], 2), number_format($totalSales[$companyId], 2), 'general', 'Geral', getCompanyAlias($companyId), number_format($totalPercentValue, 2), number_format($totalPercentAccrued, 2), 'general');
                    ?>
                    <div class="chart-label fw-bold fs-4">Geral</div>
                </div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views\goal-sales\single.blade.php ENDPATH**/ ?>