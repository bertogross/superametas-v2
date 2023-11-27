<?php
    $uniqueDepartments = $totalSales = $totalGoals = $totalPercent = [];
?>

<div id="load-listing" class="mb-4 rounded position-relative toogle_zoomInOut ribbon-box border ribbon-fill shadow-none <?php if(!$data): ?> d-none <?php endif; ?>">
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
                            <?php echo e(getCompanyNameById(intval($companyId))); ?>

                        </th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $companyId => $departments): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $departmentId => $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $uniqueDepartments[$departmentId] = getDepartmentNameById(intval($departmentId));
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

                                $sales = isset($departments[$departmentId]['sales']) ? floatval($departments[$departmentId]['sales']) : 0;
                                $goal = isset($departments[$departmentId]['goal']) ? floatval($departments[$departmentId]['goal']) : 0;

                                $percent = $sales > 0 && $goal > 0 ? ($sales / $goal) * 100 : 0;

                                $percentAccrued = $percent > 0 && $metricNumber > 0 ? ($percent / $metricNumber) * 100 : 0;

                                // Calculate the sum of sales and goals for each company
                                $totalSales[$companyId] = isset($totalSales[$companyId]) ? floatval($totalSales[$companyId]) + $sales : 0;
                                $totalGoals[$companyId] = isset($totalGoals[$companyId]) ? floatval($totalGoals[$companyId]) + $goal : 0;

                                $totalPercent[$companyId] = $totalSales[$companyId] > 1 && $totalGoals[$companyId] > 1 ? ($totalSales[$companyId] / $totalGoals[$companyId]) * 100 : 0;

                            ?>
                            <td class="text-center align-middle" data-company-id="<?php echo e($companyId); ?>" data-chart-id="<?php echo e($ndxChartId); ?>">
                                <?php
                                    echo goalsEmojiChart($ndxChartId, $goal, $sales, $departmentId, getDepartmentNameById($departmentId), getCompanyNameById($companyId), $percent, $percentAccrued);
                                ?>
                            </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php if(count($getActiveDepartments) > 1): ?>
                    <tr tr-department="sum" class="">
                        <th scope="row" class="text-uppercase fs-16 align-middle text-end p-3 <?php if(!empty($filterDepartments) && count($filterDepartments) == 1): ?> d-none <?php endif; ?>">
                            GERAL
                        </th>
                        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $companyId => $departments): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $ndxChartId++;

                                $totalPercentValue = isset($totalPercent[$companyId]) ? floatval($totalPercent[$companyId]) : 0;
                                $totalPercentAccrued = $totalPercentValue > 0 ? ($totalPercentValue / $metricNumber) * 100 : 0;
                            ?>
                            <td class="text-center align-middle <?php if(!empty($filterDepartments) && count($filterDepartments) == 1): ?>  d-none <?php endif; ?>" data-company-id="<?php echo e($companyId); ?>" data-chart-id="<?php echo e($ndxChartId); ?>">
                                <?php
                                    echo goalsEmojiChart($ndxChartId, floatval($totalGoals[$companyId]), floatval($totalSales[$companyId]), 'general', 'Geral', getCompanyNameById($companyId), $totalPercentValue, $totalPercentAccrued, 'general');
                                ?>
                            </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                <?php endif; ?>
            </tbody>
            
        </table>
    </div>
</div>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/goal-sales/table.blade.php ENDPATH**/ ?>