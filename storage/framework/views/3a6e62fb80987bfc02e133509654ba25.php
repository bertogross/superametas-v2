<?php
    $userId = getUserData()['id'];

    $totalPercentAccrued = $ndxChartId = 0;

    $getAuthorizedCompanies = getAuthorizedCompanies();
    //APP_print_r($getAuthorizedCompanies);
    $getActiveCompanies = getActiveCompanies();
    //APP_print_r($getActiveCompanies);
    $getActiveDepartments = getActiveDepartments();
    //APP_print_r($getActiveDepartments);

    //$getMeantime = request('meantime', date('Y-m'));
    //$getCustomMeantime = request('getCustomMeantime');

    $getMeantime = !empty($_REQUEST['meantime']) ? $_REQUEST['meantime'] : date('Y-m');
    $getCustomMeantime = !empty($_REQUEST['custom_meantime']) ? $_REQUEST['custom_meantime'] : '';
    $getMeantime = $getMeantime == 'custom' && empty($getCustomMeantime) ? date('Y-m') : $getMeantime;

    $explode = !empty($getCustomMeantime) ? explode(' até ', $getCustomMeantime) : '';
    $explodeMeantime = !empty($explode) && is_array($explode) && count($explode) > 1 ? $explode : $getCustomMeantime;

    //$filterCompanies = request('companies', array());
    $filterCompanies = isset($_REQUEST['companies']) ? $_REQUEST['companies'] : array();
    //$filterDepartments = request('departments', array());
    $filterDepartments = isset($_REQUEST['departments']) ? $_REQUEST['departments'] : array();

    $metric = metricGoalSales($getMeantime);
    $metricNumber = convertToNumeric($metric);

    $dateRange = getSaleDateRange();
    $firstDate = $dateRange['first_date'];
    $lastDate = $dateRange['last_date'];
    $currentMonth = now()->format('Y-m');
    $previousMonth = now()->subMonth()->format('Y-m');
?>


<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.goal-sales'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(URL::asset('build/libs/gridjs/theme/mermaid.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(URL::asset('build/libs/swiper/swiper-bundle.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(URL::asset('build/libs/flatpickr/plugins/monthSelect/style.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(URL::asset('build/libs/sweetalert2/sweetalert2.min.css')); ?>" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.goal-sales-nav'); ?>
        <?php $__env->slot('url'); ?>
            <?php echo e(url('goal-sales')); ?>

        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            <?php echo app('translator')->get('translation.goal-sales'); ?>
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('getMeantime'); ?>
            <?php echo e($getMeantime); ?>

        <?php $__env->endSlot(); ?>
        <?php $__env->slot('getCustomMeantime'); ?>
            <?php echo e($getCustomMeantime); ?>

        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div id="filter" class="p-3 bg-light-subtle rounded position-relative mb-4" style="z-index: 3;">
        <form action="<?php echo e(route('goalSalesIndexURL')); ?>" class="row g-2" autocomplete="off">

            <div class="col-sm-12 col-md-2 col-lg-auto">
                <select class="form-control form-select" name="meantime" title="Selecione o período">
                    <option <?php echo e($getMeantime == 'today' ? 'selected' : ''); ?> value="today">HOJE</option>

                    <option <?php echo e($getMeantime == $currentMonth || $getMeantime == date('Y-m') || ( $getMeantime == 'custom' && empty($getCustomMeantime) )  ? 'selected' : ''); ?> value="<?php echo e($currentMonth); ?>">MÊS ATUAL</option>

                    <?php if($firstDate <= $previousMonth): ?>
                        <option <?php echo e($getMeantime == $previousMonth ? 'selected' : ''); ?> value="<?php echo e($previousMonth); ?>">MÊS ANTERIOR</option>
                    <?php endif; ?>

                    <option <?php if($getMeantime == 'custom' && !empty($getCustomMeantime)): ?> selected <?php endif; ?> value="custom">CUSTOMIZADO</option>
                </select>
            </div>

            <div class="col-sm-12 col-md-auto col-lg-auto custom_meantime_is_selected" style="min-width:270px; <?php if(empty($getCustomMeantime)): ?> display:none; <?php endif; ?> ">
                <input type="text" class="form-control flatpickr-range-month" name="custom_meantime" data-min-date="<?php echo e($firstDate); ?>"
                data-max-date="<?php echo e($lastDate); ?>" value="<?php if($getMeantime == 'custom'): ?><?php echo e($getCustomMeantime); ?><?php endif; ?>" placeholder="Selecione o Período">
            </div>

            <?php if(!empty($getAuthorizedCompanies) && is_array($getAuthorizedCompanies) && count($getAuthorizedCompanies) > 1): ?>
                <div class="col-sm-12 col-md col-lg" title="Exibir somente Lojas selecionadas">
                    <select class="form-control" data-choices data-choices-removeItem name="companies[]" id="filter-companies" multiple data-placeholder="Loja">
                        <?php $__currentLoopData = $getAuthorizedCompanies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option <?php echo e(in_array($company, $filterCompanies) ? 'selected' : ''); ?> value="<?php echo e($company); ?>"><?php echo e(getCompanyAlias(intval($company))); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            <?php endif; ?>

            <?php if(!empty($getActiveDepartments) && is_object($getActiveDepartments) && count($getActiveDepartments) > 1): ?>
                <div class="col-sm-12 col-md col-lg" title="Exibir somente Departamentos selecionados">
                    <select class="form-control" data-choices data-choices-removeItem name="departments[]" multiple data-placeholder="Departamento">
                        <?php $__currentLoopData = $getActiveDepartments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option <?php echo e(in_array($department->department_id, $filterDepartments) ? 'selected' : ''); ?> value="<?php echo e($department->department_id); ?>"><?php echo e($department->department_alias); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            <?php endif; ?>

            <div class="col-sm-12 col-md-auto col-lg-auto wrap-form-btn">
                <button type="submit" class="btn btn-theme w-100 init-loader" title="Filtrar"><i class="ri-equalizer-fill me-1 align-bottom"></i> Filtrar</button>
            </div>
        </form>
    </div>

    <?php if(!$data): ?>
        <div class="alert alert-warning alert-label-icon label-arrow fade show" role="alert">
            <i class="ri-alert-fill label-icon"></i>Não há dados
        </div>
    <?php endif; ?>

    <?php if(getUserMeta($userId, 'analytic-mode') == 'on'): ?>
        <?php echo $__env->make('goal-sales/analytic', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php elseif(getUserMeta($userId, 'slide-mode') == 'on'): ?>
        <?php if(count($filterCompanies) == 1 || count($getAuthorizedCompanies) == 1): ?>
            <?php echo $__env->make('goal-sales/single', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php else: ?>
            <?php echo $__env->make('goal-sales/slide', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>
    <?php else: ?>
        <?php if(count($filterCompanies) == 1 || count($getAuthorizedCompanies) == 1): ?>
            <?php echo $__env->make('goal-sales/single', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php else: ?>
            <?php echo $__env->make('goal-sales/table', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>
    <?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('build/libs/choices.js/public/assets/scripts/choices.min.js')); ?>"></script>


    <script src="<?php echo e(URL::asset('build/libs/sweetalert2/sweetalert2.min.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('build/libs/flatpickr/flatpickr.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/flatpickr/l10n/pt.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/flatpickr/plugins/monthSelect/index.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('build/libs/apexcharts/apexcharts.min.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('build/js/goal-sales.js')); ?>" type="module"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views/goal-sales/index.blade.php ENDPATH**/ ?>