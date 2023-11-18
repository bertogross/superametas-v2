<?php
    //appPrintR($data);
    //appPrintR($departments);
    //appPrintR($resultsSales);
    //appPrintR($resultsGoals);
    //appPrintR($dateTickLabels);
    /**
     * Split Goals for daily view
     */
    if(strlen($getMeantime) == 7 || strlen($getCustomMeantime) == 7){
        $dailyGoalsByMonth = [];

        $daysInMonth = !empty($getMeantime) ? intval(date('t', strtotime($getMeantime))) : intval(date('t', strtotime($getCustomMeantime)));

        $period = array_keys($totalGoalsByMonth)[0];
        $explodePeriod = explode('/', $period);
        $year = $explodePeriod[2];
        $month = $explodePeriod[1];

        $totalGoal = array_values($totalGoalsByMonth)[0];
        $dailyGoal = $totalGoal / $daysInMonth;

        foreach (range(1, $daysInMonth) as $day) {
            $formattedDay = sprintf('%02d', $day);
            $date = $formattedDay.'/'.$month.'/'.$year;
            $dailyGoalsByMonth[$date] = $dailyGoal;
        }

        $totalGoalsByMonth = $dailyGoalsByMonth;
    }else{
        // For Goals, fill in the missing keys with a value of 0
        $missingGoalKeys = array_diff_key($totalSalesByMonth, $totalGoalsByMonth);
        $missingGoals = array_fill_keys(array_keys($missingGoalKeys), 0);
        $totalGoalsByMonth = array_merge($missingGoals, $totalGoalsByMonth);

        // For Sales, fill in the missing keys with a value of 0
        $missingSalesKeys = array_diff_key($totalGoalsByMonth, $totalSalesByMonth);
        $missingSales = array_fill_keys(array_keys($missingSalesKeys), 0);
        $totalSalesByMonth = array_merge($missingSales, $totalSalesByMonth);
    }
    //appPrintR($totalGoalsByMonth);
    //appPrintR($totalSalesByMonth);
?>
<div class="row" id="load-listing">
    <div class="col-xxl-3">
        <div class="card">
            <div class="card-body p-3">

                <div class="text-center">
                    <h5 class="mb-1 text-uppercase">Departamentos</h5>

                    <div class="mt-3">
                        <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="row align-items-center g-2">
                                <div class="col">
                                    <div class="p-1">
                                        <h6 class="mb-0 text-end"><?php echo e($department->name); ?></h6>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="p-1">
                                        <div class="progress animated-progress progress-sm" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo e($department->progress); ?>" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" title="<?php echo e($department->tooltip); ?>" tabindex="0">
                                            <div class="progress-bar bg-<?php echo e($department->color); ?>" style="width: <?php echo e($department->progress); ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="p-1">
                                        <h6 class="mb-0 text-muted text-start"><?php echo e(formatBrazilianReal($department->sales, 2)); ?></h6>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
            <!--end card-body-->

        </div>
        <!--end card-->
    </div>

    <div class="col-xxl-9">
        <div class="card">
            <div class="card-header border-0">
                <div class="row">
                    <h4 class="card-title col-6 mb-0 text-uppercase">
                        <span class="me-2">Período: </span>
                        <span class="text-theme small ms-2">
                        <?php
                            if (is_array($explodeMeantime) && count($explodeMeantime) > 1) {
                                echo utf8_encode(ucfirst(strftime("%B/%Y", strtotime($explodeMeantime[0]))));

                                echo '<i class="ri-arrow-left-right-line text-body me-1 ms-1 align-middle small"></i>';

                                echo utf8_encode(ucfirst(strftime("%B/%Y", strtotime($explodeMeantime[1]))));

                            }elseif($getMeantime == 'custom' && !empty($getCustomMeantime)){
                                echo utf8_encode(ucfirst(strftime("%B/%Y", strtotime($getCustomMeantime))));
                            }elseif( $getMeantime == 'today' ){
                                echo 'Hoje, <span class="small text-body">'.date("d/m/Y", strtotime($getMeantime)).'</span>';
                            }else{
                                echo utf8_encode(ucfirst(strftime("%B/%Y", strtotime($getMeantime))));
                            }

                            $plural = count($filterCompanies) > 1 ? 's' : '';
                        ?>
                        </span>
                    </h4>
                    <span class="col-6 small text-body text-end">
                        <?php
                        echo count($filterCompanies) > 0 ? '<span class="text-theme fw-bold me-1">'.count($filterCompanies).'</span> Loja'.$plural.' Selecionada'.$plural.'' : 'Todas as Lojas';
                        ?>
                    </span>
                </div>
            </div>

            <div class="card-header p-0 border-0 bg-light-subtle">
                <div class="row g-0 text-center">
                    <!--
                    <div class="col-6 col-md-3">
                        <div class="p-3 border border-dashed border-start-0">
                            <h5 class="mb-1">
                                <span>
                                    <?php echo e($metric); ?>

                                </span>%
                            </h5>
                            <p class="text-muted mb-0">Acumulado</p>
                        </div>
                    </div>
                    -->
                    <div class="col">
                        <div class="p-3 border border-dashed border-start-0" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-title="Vendas" data-bs-content="Você está visualizando o valor somado de <?php echo e(count($filterCompanies) > 0 ? '<span class="text-theme fw-bold me-1">'.count($filterCompanies).'</span> Loja'.$plural.'' : 'Todas as Lojas'); ?>">
                            <h5 class="mb-1">
                                <span>
                                    <?php echo e(is_array($totalSalesByMonth) && array_sum($totalSalesByMonth) > 0 ? formatBrazilianReal( array_sum($totalSalesByMonth), 0) : 0); ?>

                                </span>
                            </h5>
                            <p class="text-muted mb-0">Vendas</p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-3 border border-dashed border-start-0" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-title="Meta" data-bs-content="Você está visualizando o valor somado de <?php echo e(count($filterCompanies) > 0 ? '<span class="text-theme fw-bold me-1">'.count($filterCompanies).'</span> Loja'.$plural.'' : 'Todas as Lojas'); ?>">
                            <h5 class="mb-1">
                                <span>
                                    <?php echo e(is_array($totalGoalsByMonth) && array_sum($totalGoalsByMonth) > 0 ? formatBrazilianReal( array_sum($totalGoalsByMonth), 0) : 0); ?>

                                </span>
                            </h5>
                            <p class="text-muted mb-0">Meta</p>
                        </div>
                    </div>
                    
                </div>
            </div>
            <div class="card-body p-0 pb-2">
                <?php
                    $dataMin = is_array($totalGoalsByMonth) && is_array($totalSalesByMonth) ? min( array_merge(array_values($totalGoalsByMonth), array_values($totalSalesByMonth)) ) : 0;

                    $dataMax = is_array($totalGoalsByMonth) && is_array($totalSalesByMonth) ? max( array_merge(array_values($totalGoalsByMonth), array_values($totalSalesByMonth)) ) : 0;

                    $dateTickLabels = array_merge(array_keys($totalGoalsByMonth), array_keys($totalSalesByMonth));
                    $dataMeantime = is_array($dateTickLabels) ? implode(',', $dateTickLabels) : '';

                    $dataGoals = isset($totalGoalsByMonth) && is_array($totalGoalsByMonth) ? implode(',', $totalGoalsByMonth) : 0;

                    $dataSales = isset($totalSalesByMonth) && is_array($totalSalesByMonth) ? implode(',', $totalSalesByMonth) : 0;

                    $dataTick = isset($dateTickLabels) && is_array($dateTickLabels) ? count($dateTickLabels) : 5;
                ?>
                <div id="goal-sales-chart-area"
                    data-colors='["--vz-theme", "--vz-info"]'
                    data-min="<?php echo e($dataMin); ?>"
                    data-max="<?php echo e($dataMax); ?>"
                    data-meantime="<?php echo e($dataMeantime); ?>"
                    data-goal="<?php echo e($dataGoals); ?>"
                    data-sale="<?php echo e($dataSales); ?>"
                    data-tick="<?php echo e($dataTick); ?>"
                    dir="ltr">
                </div>

            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-card gridjs-border-none">TODO TABLE HERE</div>
            </div>
        </div>

    </div>
</div>

<?php $__env->startSection('script-bottom'); ?>
    <script src="<?php echo e(URL::asset('build/js/pages/seller-details.init.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/goal-sales/analytic.blade.php ENDPATH**/ ?>