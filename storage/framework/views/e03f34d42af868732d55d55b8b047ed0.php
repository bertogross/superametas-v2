<?php
    use App\Models\User;

    $ndx = 1;

    $buttonIPCAtext = '';

    $buttonIPCAtext .= !empty($getIPCA) ? "Executar o Autopreenchimento dos campos considerando o valor de <span class='text-info fw-bold'>".date('m/Y', strtotime('-12 months', strtotime($meantime)))."</span> acrescido da Variação IPCA <span class='text-info fw-bold'>".date('m/Y', strtotime($getIPCA['period']))."</span><br><br><span class='text-theme fw-bold fs-16'>".numberFormat($getIPCA['value'], 2)."%</span>" : '';

    $buttonIPCAtext .= !empty($getIPCA) && !empty($goals) ? "<div class='alert alert-warning alert-border-left alert-dismissible fade show p-1 mt-2 mb-1 small'>Campos outrora preenchidos terão seus valores substituídos</div>" : '';


    //appPrintR($goals);
    //appPrintR($salesYearBefore);
    //appPrintR($salesMonthBefore);
?>
<div class="modal flip" id="goalSalesEditModal" tabindex="-1" data-bs-backdrop="static" data-bs-focus="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header p-3 bg-soft-info">
                <h5 class="modal-title">Gerenciar Meta</h5>
                <button type="button" class="btn-close btn-destroy" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                <?php
                    if( onlyNumber($meantime) > onlyNumber(date('Ym')) ){
                        echo '<div class="alert alert-solid alert-label-icon alert-warning mb-3 small"><i class="ri-alert-line label-icon"></i>O valor REALIZADO de <u>'.strftime(date("m/Y", strtotime($previousMeantimeMonthBefore))).'</u> não é efetivo pois o período ainda não foi encerrado.</div>';
                    }
                    if( empty($salesYearBefore) ){
                        echo '<div class="alert alert-solid alert-label-icon alert-info mb-3 small"><i class="ri-alert-line label-icon"></i><strong class="text-uppercase">Atenção</strong><br>Não há dados do REALIZADO de <strong>'.strftime(date("m/Y", strtotime($previousMeantimeYearBefore))).'</strong> e por isso o Autopreenchimento foi desabilitado.</div>';
                    }
                    if( $getIPCA['period'] != date("Y-m", strtotime($meantime)) ){
                        echo '<div class="alert alert-solid alert-label-icon alert-danger mb-3 small"><i class="ri-alert-line label-icon"></i><strong class="text-uppercase">Atenção</strong><br>IPCA <u>'.date("m/Y", strtotime($meantime)).'</u> indisponível.<br>Portanto, o Autopreenchimento irá calcular sobre o dado forcecido pelo IBGE e relacionado ao período recente: <u>'.date("m/Y", strtotime($getIPCA['period'])).'</u>.</div>';
                    }
                ?>

                <form id="goalSalesForm" method="POST" autocomplete="off">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="meantime" value="<?php echo e($meantime); ?>">
                    <input type="hidden" name="company" value="<?php echo e($companyId); ?>">
                    <input type="hidden" name="type" value="sales">

                    <div class="card mt-0 mb-0">
                        <div class="card-header pe-0 ps-0 pt-0 border-bottom-0">
                            <?php if($getIPCA): ?>
                                <button type="button" id="btn-ipca-self-fill" class="btn btn-sm btn-outline-theme float-end" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" title="<?php echo e($buttonIPCAtext); ?>" data-swal-title="<?php echo e($buttonIPCAtext); ?>" data-ipca-value="<?php echo e(!empty($getIPCA['value']) ? $getIPCA['value'] : ''); ?>" data-from=".o-sum-fields-previous-year" data-to=".o-sum-fields-current">Autopreenchimento</button>
                            <?php endif; ?>

                            <h6 class="card-title text-theme mb-3"><?php echo e(ucfirst(strftime("%B/%Y", strtotime($meantime)))); ?> </h6>
                        </div>
                        <div class="card-body pe-0 ps-0 pt-0 pb-0">
                            <div class="table-responsive border border-1 border-light rounded-2">
                                <table class="table table-sm table-bordered table-striped mb-0" data-company-id="<?php echo e($companyId); ?>">
                                    <thead class="table-light text-uppercase">
                                        <tr>
                                            <th scope="col" class="text-end">Departamento</th>
                                            <th scope="col" class="text-center text-warning" width="190">
                                                <div class="small">
                                                    <?php echo e(ucfirst(strftime("%B/%Y", strtotime($previousMeantimeYearBefore)))); ?>

                                                </div>
                                                Realizado
                                                <div class="h6 m-0 text-muted sum-result-previous-year">
                                                    <?php echo e(!empty($salesYearBefore) && is_array($salesYearBefore) ? numberFormat(array_sum($salesYearBefore), 0) : ''); ?>

                                                </div>
                                            </th>
                                            <th scope="col" class="text-center text-theme" width="190">
                                                <div class="small">
                                                    <?php echo e(ucfirst(strftime("%B/%Y", strtotime($previousMeantimeMonthBefore)))); ?>

                                                </div>
                                                Realizado
                                                <div class="h6 m-0 text-muted sum-result-previous-month">
                                                    <?php echo e(!empty($salesMonthBefore) && is_array($salesMonthBefore) ? numberFormat(array_sum($salesMonthBefore), 0) : ''); ?>

                                                </div>
                                            </th>
                                            <th scope="col" class="text-center" width="190">
                                                <div class="small text-info text-opacity-100">
                                                    <?php echo e(ucfirst(strftime("%B/%Y", strtotime($meantime)))); ?>

                                                </div>
                                                <div class="small text-info text-opacity-100">Meta</div>
                                                <div class="h6 m-0 sum-result-current"></div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $getActiveDepartments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td class="text-end align-middle" data-dep="<?php echo e($department->department_id); ?>"><?php echo e($department->department_alias); ?></td>
                                                <td class="text-center align-middle o-sum-fields-previous-year">
                                                    <?php echo e(!empty($salesYearBefore[$department->department_id]) ? numberFormat($salesYearBefore[$department->department_id], 0) : 0); ?>

                                                </td>
                                                <td class="text-center align-middle o-sum-fields-previous-month">
                                                    <?php echo e(!empty($salesMonthBefore[$department->department_id]) ? numberFormat($salesMonthBefore[$department->department_id], 0) : 0); ?>

                                                </td>
                                                <td class="align-middle input-group input-group-sm">
                                                    <span class="input-group-text">R$</span>
                                                    <input type="text"class="form-control o-sum-fields-current format-numbers" name="goals[<?php echo e($department->department_id); ?>]" value="<?php echo e($goals[$department->department_id] ?? ''); ?>" maxlength="20" tabindex="<?php echo e($ndx++); ?>">
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="wrap-form-btn w-100 d-none">
                            <button type="button" class="btn btn-theme mt-2 float-end w-100" id="btn-goal-sales-update">Adicionar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views\goal-sales\edit.blade.php ENDPATH**/ ?>