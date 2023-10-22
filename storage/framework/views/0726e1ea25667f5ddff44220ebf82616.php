<?php
use App\Models\User;

$getActiveDepartments = getActiveDepartments();

$ndx = 1;

$type = 'sales';

$meantime = request('meantime');
$companyId = request('companyId');

// Query the wlsm_goals table to get the goals for the given companyId and meantime
$goals = DB::connection('smAppTemplate')
    ->table('wlsm_goals')
    ->where('company_id', $companyId)
    ->where('meantime', $meantime)
    ->where('type', $type)
    ->get()
    ->pluck('goal_value', 'department_id')
    ->toArray();
//APP_print_r($goals);
?>
<div class="modal flip" id="goalSalesEditModal" tabindex="-1" data-bs-backdrop="static" data-bs-focus="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header p-3 bg-soft-info">
                <h5 class="modal-title">Gerenciar Meta</h5>
                <button type="button" class="btn-close btn-destroy" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                

                <form id="goalSalesForm" method="POST" autocomplete="off">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="meantime" value="<?php echo e($meantime); ?>">
                    <input type="hidden" name="company" value="<?php echo e($companyId); ?>">
                    <input type="hidden" name="type" value="<?php echo e($type); ?>">

                    <div class="card mt-0 mb-0">
                        <div class="card-header pe-0 ps-0 pt-0 border-bottom-0">
                            <button type="button" id="btn-ipca-self-fill" data-decimal="0"
                                class="btn btn-sm btn-outline-theme float-end" data-bs-toggle="tooltip"
                                data-bs-placement="top" data-bs-html="true" data-ipca-period="2023-09"
                                data-ipca-value="5.19" data-previous-meantime="11/2022"
                                data-from=".o-sum-fields-previous-year" data-to="input.o-sum-fields"
                                data-bs-original-title="Executar o Autopreenchimento dos campos considerando a Variação IPCA <span class='text-info fw-bold'>09/2023</span><br><br><span class='text-theme fw-bold fs-16'>5.19%</span>">Autopreenchimento</button>
                            <h6 class="card-title text-theme mb-3"><?php echo e(ucfirst(strftime("%B/%Y", strtotime($meantime)))); ?> </h6>
                        </div>
                        <div class="card-body pe-0 ps-0 pt-0 pb-0">
                            <div class="table-responsive border border-1 border-light rounded-2">
                                <table class="table table-sm table-bordered table-striped mb-0" data-company-id="<?php echo e($companyId); ?>">
                                    <thead class="table-light text-uppercase">
                                        <tr>
                                            <th scope="col" class="text-end">Departamento</th>
                                            <th scope="col" class="text-center text-theme d-none" width="190">
                                                <div class="small">11/2022</div>
                                                Realizado
                                                <div class="h6 m-0 text-muted" id="sum-result-previous"></div>
                                            </th>
                                            <th scope="col" class="text-center text-theme" width="190">
                                                <div class="small">10/2023</div>
                                                Realizado
                                                <div class="h6 m-0 text-muted" id="sum-result-previous"></div>
                                            </th>
                                            <th scope="col" class="text-center" width="190">
                                                <div class="small text-info text-opacity-100"><?php echo e(date('m/Y', strtotime($meantime))); ?></div>
                                                <div class="small text-info text-opacity-100">Meta</div>
                                                <div class="h6 m-0" id="sum-result"></div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $getActiveDepartments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td class="text-end align-middle" data-dep="<?php echo e($department->department_id); ?>"><?php echo e($department->department_alias); ?></td>
                                                <td class="text-center align-middle d-none o-sum-fields-previous-year"></td>
                                                <td class="text-center align-middle o-sum-fields-previous"></td>
                                                <td class="align-middle input-group input-group-sm">
                                                    <span class="input-group-text">R$</span>
                                                    <input type="text"class="form-control o-sum-fields format-numbers" name="goals[<?php echo e($department->department_id); ?>]" value="<?php echo e($goals[$department->department_id] ?? ''); ?>" maxlength="50" tabindex="<?php echo e($ndx++); ?>">
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="wrap-form-btn w-100">
                            <button type="button" class="btn btn-theme mt-2 float-end w-100" id="btn-goal-sales-update">Adicionar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views/goal-sales/edit-modal.blade.php ENDPATH**/ ?>