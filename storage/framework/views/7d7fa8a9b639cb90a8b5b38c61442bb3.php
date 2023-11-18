<?php
//phpinfo();
//exit;
?>

<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.your-erp'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('url'); ?>
            <?php echo e(url('settings')); ?>

        <?php $__env->endSlot(); ?>
        <?php $__env->slot('li_1'); ?>
            <?php echo app('translator')->get('translation.settings'); ?>
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            <?php echo app('translator')->get('translation.your-erp'); ?>
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <?php echo $__env->make('components.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- resources/views/settings/database.blade.php -->

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-2">
                    <div class="nav nav-pills flex-column nav-pills-tab verti-nav-pills custom-verti-nav-pills nav-pills-theme" role="tablist" aria-orientation="vertical">
                        <a class="nav-link text-uppercase <?php echo e(session('active_tab') == 'departments' || session('active_tab') == '' ? 'active show' : ''); ?>" id="v-pills-departments-tab" data-bs-toggle="pill" href="#v-pills-departments" role="tab" aria-controls="v-pills-departments"
                            aria-selected="true">
                            Departamentos</a>
                        <a class="nav-link text-uppercase <?php echo e(session('active_tab') == 'companies' ? 'active show' : ''); ?>" id="v-pills-companies-tab" data-bs-toggle="pill" href="#v-pills-companies" role="tab" aria-controls="v-pills-companies"
                            aria-selected="false">
                            Empresas</a>
                        <a class="nav-link text-uppercase <?php echo e(session('active_tab') == 'synchronization' ? 'active show' : ''); ?>" id="v-pills-synchronization-tab" data-bs-toggle="pill" href="#v-pills-synchronization" role="tab" aria-controls="v-pills-synchronization"
                            aria-selected="false">
                            Sincronizacão</a>
                    </div>
                </div> <!-- end col-->
                <div class="col-lg-10">
                    <div class="tab-content text-muted mt-3 mt-lg-0">
                        <div class="tab-pane fade <?php echo e(session('active_tab') == 'departments' || session('active_tab') == '' ? 'active show' : ''); ?>" id="v-pills-departments" role="tabpanel" aria-labelledby="v-pills-departments-tab">
                            <form action="<?php echo e(route('settingsDepartmentsUpdateURL')); ?>" method="POST" autocomplete="off">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>
                                <button type="submit" class="btn btn-theme float-end">Atualizar Departamentos</button>

                                <h2 class="text-body mb-2 h4">Departamentos</h2>
                                <p>Renomeie cada dos departamentos caso entenda que será necessário para fins de exibição em relatórios </p>
                                <div class="table-responsive border border-1 border-dark rounded rounded-2">
                                    <table class="table table-striped mb-0">
                                        <thead class="table-light text-uppercase">
                                            <tr>
                                                <th width="65" class="text-center"></th>
                                                <th width="130">ID</th>
                                                <th>Departamento</th>
                                                <th>Alias</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr class="main-row" data-id="<?php echo e($department->id); ?>">
                                                    <td class="align-middle">
                                                        <!-- Checkbox for Status Update -->
                                                        <div class="form-check form-switch form-switch-md form-switch-theme text-end">
                                                            <input type="hidden" name="status[<?php echo e($department->id); ?>]" value="0">

                                                            <input type="checkbox"
                                                            class="form-check-input"
                                                            value="1"
                                                            name="status[<?php echo e($department->id); ?>]"
                                                            data-id="<?php echo e($department->id); ?>"
                                                            <?php echo e($department->status == 1 ? 'checked' : ''); ?>>
                                                        </div>
                                                    </td>
                                                    <td class="align-middle">
                                                        <?php echo e(e($department->department_id)); ?>

                                                    </td>
                                                    <td class="align-middle">
                                                        <?php echo $department->department_description; ?>

                                                    </td>
                                                    <td class="align-middle">
                                                        <input type="text" name="aliases[<?php echo e($department->id); ?>]" value="<?php echo e(empty($department->department_alias) ? e(strip_tags($department->department_description )) : e(strip_tags($department->department_alias))); ?>" maxlength="100" class="form-control">
                                                    </td>
                                                    
                                                </tr>
                                                
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        </div><!--end tab-pane-->
                        <div class="tab-pane fade <?php echo e(session('active_tab') == 'companies' ? 'active show' : ''); ?>" id="v-pills-companies" role="tabpanel" aria-labelledby="v-pills-companies-tab">
                            <form action="<?php echo e(route('settingsCompaniesUpdateURL')); ?>" method="POST" autocomplete="off">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>
                                <button type="submit" class="btn btn-theme float-end">Atualizar Empresas</button>

                                <h2 class="text-body mb-2 h4">Empresas</h2>
                                <p>Renomeie cada das empresas caso entenda que será necessário para fins de exibição em relatórios </p>
                                <div class="table-responsive border border-1 border-dark rounded rounded-2">
                                    <table class="table table-striped mb-0">
                                        <thead class="table-light text-uppercase">
                                            <tr>
                                                <th width="65" class="text-center"></th>
                                                <th width="130">ID</th>
                                                <th>Empresa</th>
                                                <th>Alias</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td class="align-middle">
                                                        <!-- Checkbox for Status Update -->
                                                        <div class="form-check form-switch form-switch-md form-switch-theme text-end">
                                                            <input type="hidden" name="status[<?php echo e($company->id); ?>]" value="0">

                                                            <input type="checkbox"
                                                            class="form-check-input"
                                                            name="status[<?php echo e($company->id); ?>]" value="1"
                                                            data-id="<?php echo e($company->id); ?>"
                                                            <?php echo e($company->status == 1 ? 'checked' : ''); ?>>
                                                        </div>
                                                    </td>
                                                    <td class="align-middle">
                                                        <?php echo e(e($company->company_id)); ?>

                                                    </td>
                                                    <td class="align-middle">
                                                        <?php echo $company->company_name; ?>

                                                    </td>
                                                    <td class="align-middle">
                                                        <input type="text" name="aliases[<?php echo e($company->id); ?>]" value="<?php echo e(empty($company->company_alias) ? e($company->company_name) : e($company->company_alias)); ?>" maxlength="100" class="form-control">
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        </div><!--end tab-pane-->
                        <div class="tab-pane fade <?php echo e(session('active_tab') == 'synchronization' ? 'active show' : ''); ?>" id="v-pills-synchronization" role="tabpanel" aria-labelledby="v-pills-synchronization-tab">
                            <button type="button" id="btn-start-synchronization" class="btn btn-theme float-end">Sincronizar</button>

                            <h2 class="text-body mb-2 h4">Sincronização da Base de Dados</h2>
                            <p>Clique em Sincronizar para efetuar a pré carga</p>

                            <div id="synchronization-progress" class="card bg-light overflow-hidden d-none">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">
                                                <b class="text-theme synchronization-percent me-2 d-none">0%</b>
                                                <span class="synchronization-percent-text"></span>
                                            </h6>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <h6 class="synchronization-time mb-0"></h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="progress bg-warning-subtle rounded-0">
                                    <div class="progress-bar bg-warning synchronization-percent" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <ul class="list-unstyled concluded-meantimes">
                            </ul>
                        </div><!--end tab-pane-->
                    </div>
                </div> <!-- end col-->
            </div> <!-- end row-->
        </div><!-- end card-body -->
    </div><!--end card-->

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>

    <script src="<?php echo e(URL::asset('build/js/settings-database.js')); ?>" type="module"></script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/settings/database.blade.php ENDPATH**/ ?>