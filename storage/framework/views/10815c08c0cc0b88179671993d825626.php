<?php
use App\Models\User;

$type = 'sales';

$getAuthorizedCompanies = getAuthorizedCompanies();
//APP_print_r($getAuthorizedCompanies);
$getActiveCompanies = getActiveCompanies();
//APP_print_r($getActiveCompanies);
$getActiveDepartments = getActiveDepartments();
//APP_print_r($getActiveDepartments);

$dateRange = getSaleDateRange();
$firstDate = $dateRange['first_date'];
$lastDate = $dateRange['last_date'];
$currentMonth = now()->format('Y-m');
$previousMonth = now()->subMonth()->format('Y-m');

$startYear = date('Y', strtotime($firstDate));
$endYear = intval($currentMonth) >= (intval(date('Y'))+11) ? date('Y', strtotime($currentMonth." +1 year")) : date('Y');
?>

<div class="modal flip" id="goalSalesSettingsModal" tabindex="-1" data-bs-backdrop="static" data-bs-focus="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header p-3 bg-soft-info">
                <h5 class="modal-title">Gerenciar Metas</h5>
                <button type="button" class="btn-close btn-destroy" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                

                <?php if(!empty($getAuthorizedCompanies) && is_array($getAuthorizedCompanies)): ?>
                    <ul class="nav nav-tabs nav-border-top nav-justified" role="tablist">
                        <?php $__currentLoopData = $getAuthorizedCompanies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?php echo e($key == 0 ? 'active' : ''); ?> text-uppercase" id="company-<?php echo e($company); ?>-tab" data-bs-toggle="tab" data-bs-target="#company-<?php echo e($company); ?>" type="button" role="tab" aria-controls="company-<?php echo e($company); ?>" aria-selected="true"><?php echo e(getCompanyAlias(intval($company))); ?></button>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>

                    <div class="tab-content p-3 bg-light">
                        <?php $__currentLoopData = $getAuthorizedCompanies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="tab-pane fade show <?php echo e($key == 0 ? 'active' : ''); ?>" id="company-<?php echo e($company); ?>" role="tabpanel" aria-labelledby="company-<?php echo e($company); ?>-tab">
                                <div id="load-emp-<?php echo e($company); ?>">
                                    <div class="accordion custom-accordionwithicon custom-accordion-border accordion-border-box mt-1" id="accordion-<?php echo e($company); ?>">
                                        <?php $__currentLoopData = range($endYear, $startYear); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $aYear): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="accordion-<?php echo e($company.$aYear); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(getCompanyAlias(intval($company))); ?> ano <?php echo e($aYear); ?>">
                                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#accor-<?php echo e($company.$aYear); ?>" aria-expanded="false" aria-controls="accor-<?php echo e($company.$aYear); ?>">
                                                        <i class="ri-calendar-line me-1"></i> <?php echo e($aYear); ?> <span class="badge badge-theme badge-border ms-2" id="count-year-posts-<?php echo e($company); ?>-<?php echo e($aYear); ?>"></span>
                                                    </button>
                                                </h2>
                                                <div id="accor-<?php echo e($company.$aYear); ?>" class="accordion-collapse collapse" aria-labelledby="accordion-<?php echo e($company.$aYear); ?>" data-bs-parent="#accordion-<?php echo e($company); ?>">
                                                    <div class="accordion-body">
                                                        <table class="table table-sm table-bordered table-striped mb-0">
                                                            <tbody>
                                                                <?php
                                                                    $periods = [];
                                                                    foreach (range(12, 1) as $aMonth) {
                                                                        $periods[] = date('Y-m', strtotime($aYear.'-'.$aMonth));
                                                                    }

                                                                ?>

                                                                <?php $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $period): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <?php
                                                                        $explode = explode('-', $period);
                                                                        $year = $explode[0];
                                                                        $month = $explode[1];

                                                                        $Id = getGoalsId($company, $period, $type);
                                                                    ?>
                                                                    <tr data-meantime="<?php echo e($period); ?>" data-company="<?php echo e($company); ?>">
                                                                        <td class="align-middle ps-3">
                                                                            <span class="meantime <?php if(!empty($Id)): ?> text-theme <?php endif; ?>">
                                                                                <?php echo e($year); ?>, <?php echo e(ucfirst(strftime("%B", strtotime($period)))); ?>

                                                                            </span>
                                                                        </td>
                                                                        <td class="align-middle pe-3 ps-3" width="80">
                                                                            <?php if(empty($Id)): ?>
                                                                                <button type="button" class="btn btn-sm btn-outline-theme btn-goal-sales-edit waves-effect waves-light float-end w-100" data-meantime="<?php echo e($period); ?>" data-company-id="<?php echo e($company); ?>" data-company-name="<?php echo e(getCompanyAlias(intval($company))); ?>" data-purpose="store" title="Adicionar Meta de Vendas <?php echo e($period); ?> <?php echo e(count($getActiveCompanies) > 1 ? ':: '.getCompanyAlias(intval($company)) : ''); ?>" modal-title="Adicionar Meta de Vendas :: <span class='text-theme'><?php echo e(getCompanyAlias(intval($company))); ?></span>">Adicionar</button>
                                                                            <?php else: ?>
                                                                                <button type="button" class="btn btn-sm btn-theme btn-goal-sales-edit waves-effect waves-light float-end w-100" data-id="<?php echo e($Id); ?>" data-meantime="<?php echo e($period); ?>" data-company-id="<?php echo e($company); ?>" data-company-name="<?php echo e(getCompanyAlias(intval($company))); ?>" data-purpose="update" title="Editar Meta de Vendas <?php echo e($period); ?> <?php echo e(getCompanyAlias(intval($company))); ?>" modal-title="Editar Meta de Vendas :: <span class='text-theme'><?php echo e(getCompanyAlias(intval($company))); ?></span>">Editar</button>
                                                                            <?php endif; ?>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                <?php else: ?>
                    <div class="alert alert-warning">Empresas ainda não foram cadastradas/ativadas</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views/goal-sales/settings-modal.blade.php ENDPATH**/ ?>