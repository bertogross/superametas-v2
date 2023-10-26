<?php
use App\Models\User;

$getAuthorizedCompanies = getAuthorizedCompanies();
//APP_print_r($getAuthorizedCompanies);
$getActiveCompanies = getActiveCompanies();
//APP_print_r($getActiveCompanies);
$getActiveDepartments = getActiveDepartments();
//APP_print_r($getActiveDepartments);

$getMeantime = isset($_REQUEST['meantime']) ? $_REQUEST['meantime'] : date('Y-m');

$getCustomMeantime = isset($_REQUEST['custom_meantime']) ? $_REQUEST['custom_meantime'] : '';
$explodeCustomMeantime = $getCustomMeantime ? explode(' até ', $getCustomMeantime) : '';

$filterCompanies = isset($_REQUEST['companies']) ? $_REQUEST['companies'] : array();
$filterDepartments = isset($_REQUEST['departments']) ? $_REQUEST['departments'] : array();
$getCustomMeantime = isset($_REQUEST['custom_meantime']) ? $_REQUEST['custom_meantime'] : date('Y-m');
?>


<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.goal-sales'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(URL::asset('build/libs/gridjs/theme/mermaid.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(URL::asset('build/libs/swiper/swiper-bundle.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(URL::asset('build/libs/flatpickr/plugins/monthSelect/style.css')); ?>" rel="stylesheet">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('url'); ?>
            <?php echo e(url('/')); ?>

        <?php $__env->endSlot(); ?>
        <?php $__env->slot('li_1'); ?>
            <?php echo app('translator')->get('translation.dashboards'); ?>
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            <?php echo app('translator')->get('translation.goal-sales'); ?>
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>



    <div id="filter" class="p-3 bg-light-subtle rounded position-relative mb-4" style="z-index: 3;">
        <form id="filterForm" action="<?php echo e(route('goal-sales.index')); ?>" class="row g-2 text-uppercase" autocomplete="off">

            <div class="col-sm-12 col-md-2 col-lg-auto">
                <select class="form-control form-select" name="meantime" title="Selecione o período">
                    <option <?php echo e($getMeantime == 'today' ? 'selected' : ''); ?> value="today">HOJE</option>

                    <?php
                        $dateRange = getSaleDateRange();
                        //APP_print_r($dateRange);
                        $createdAt = $dateRange['created_at'];
                        $updatedAt = $dateRange['updated_at'];
                        $currentMonth = now()->format('Y-m');
                        $previousMonth = now()->subMonth()->format('Y-m');
                    ?>

                    <option <?php echo e($getMeantime == $currentMonth || $getMeantime == date('Y-m') || ( $getMeantime == 'custom' && empty($getCustomMeantime) )  ? 'selected' : ''); ?> value="<?php echo e($currentMonth); ?>">MÊS ATUAL</option>

                    <?php if($createdAt <= $previousMonth): ?>
                        <option <?php echo e($getMeantime == $previousMonth ? 'selected' : ''); ?> value="<?php echo e($previousMonth); ?>">MÊS ANTERIOR</option>
                    <?php endif; ?>

                    <option <?php if($getMeantime == 'custom' && !empty($getCustomMeantime)): ?> selected <?php endif; ?> value="custom">CUSTOMIZADO</option>
                </select>
            </div>

            <div class="col-sm-12 col-md-auto col-lg-auto custom_meantime_is_selected" style="min-width:270px; <?php if(empty($getCustomMeantime)): ?> display:none; <?php endif; ?> ">
                <input type="text" class="form-control flatpickr-range-month" name="custom_meantime" data-min-date="<?php echo e($createdAt); ?>"
                data-max-date="<?php echo e($updatedAt); ?>" value="<?php if($getMeantime == 'custom'): ?><?php echo e($getCustomMeantime); ?><?php endif; ?>" placeholder="Selecione o Período">
            </div>

            <?php if(!empty($getAuthorizedCompanies) && is_array($getAuthorizedCompanies) && count($getAuthorizedCompanies) > 1): ?>
                <div class="col-sm-12 col-md col-lg" title="Exibir somente Lojas selecionadas">
                    <select class="form-control" data-choices data-choices-removeItem name="companies[]" multiple data-placeholder="Loja">
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

            <div class="col-sm-12 col-md-auto col-lg-auto wrap-form-btne">
                <button type="submit" class="btn btn-theme w-100 init-loader" title="Filtrar">Filtrar</button>
            </div>
        </form>
    </div>

    <!-- resources/views/goal-sales_table.blade.php -->
    <?php echo $__env->make('goal-sales-table', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('build/libs/apexcharts/apexcharts.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/flatpickr/flatpickr.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/flatpickr/l10n/pt.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/flatpickr/plugins/monthSelect/index.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('build/js/goal-sales.js')); ?>" type="module"></script>

    <script src="<?php echo e(URL::asset('build/js/app.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views/goal-sales.blade.php ENDPATH**/ ?>
