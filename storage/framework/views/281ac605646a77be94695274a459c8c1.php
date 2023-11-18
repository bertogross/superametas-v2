<?php
appPrintR($distributedData);
?>


<div class="row">

    <div class="col-sm-12 col-md-4">
        <div class="card">
            <?php if($survey->status == 'new'): ?>
                <button type="button" class="btn btn-lg btn-soft-theme btn-icon w-100 h-100 p-4" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Iniciar Rotina <?php echo e($recurringLabel); ?>"><i class="ri-play-fill"></i></button>
            <?php else: ?>
                <button type="button" class="btn btn-lg btn-soft-danger btn-icon w-100 h-100 p-4" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Parar Rotina <?php echo e($recurringLabel); ?>"><i class="ri-stop-fill"></i></button>
            <?php endif; ?>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Informações</h5>
                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <th class="ps-0" scope="row">Data de Início :</th>
                                <td class="text-muted">dd/mm/YYYY</td>
                            </tr>
                            
                            <tr>
                                <th class="ps-0" scope="row">Falhas :</th>
                                <td class="text-muted">??</td>
                            </tr>
                            <tr>
                                <th class="ps-0" scope="row">Em Progresso :</th>
                                <td class="text-muted">??</td>
                            </tr>
                            <tr>
                                <th class="ps-0" scope="row">Concluídas :</th>
                                <td class="text-muted">??</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div><!-- end card body -->
        </div>

        
    </div>

    <div class="col-sm-12 col-md-4">
        <div class="card">
            <a class="card-body bg-info-subtle"><!-- data-bs-toggle="collapse" href="#leadDiscovered" role="button"
                aria-expanded="false" aria-controls="leadDiscovered" -->
                <h5 class="card-title text-uppercase mb-1 fs-14">
                    <?php
                        $columns = array_column($distributedData['delegated_to'], 'user_id');
                        $uniqued = count($columns) > 1 ? array_unique($columns) : $columns;
                        echo is_array($uniqued) ? count($uniqued) : 0;
                    ?>
                     Vistoriador<?php echo e(count($uniqued) > 1 ? 'es' : ''); ?>

                </h5>
                <p class="text-muted mb-0"><span class="fw-medium"><?php echo e(is_array($distributedData['delegated_to']) ? count($distributedData['delegated_to']) : ''); ?> lojas</span></p>
            </a>
        </div>
        <!--end card-->
        <div class="collapse show" id="leadDiscovered">
            <?php $__currentLoopData = $distributedData['delegated_to']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $userId = $value['user_id'];
                    $companyName = getCompanyAlias($value['company_id']);
                    $avatar = getUserData($userId)['avatar'];
                    $name = getUserData($userId)['name'];
                ?>
                <div class="card mb-1 ribbon-box ribbon-fill ribbon-sm right">
                    <div class="ribbon ribbon-primary"><i class="ri-flashlight-fill"></i></div>
                    <div class="card-body">
                        <a class="d-flex align-items-center" data-bs-toggle="collapse" href="#leadDiscovered<?php echo e($index); ?>" role="button"
                            aria-expanded="false" aria-controls="leadDiscovered<?php echo e($index); ?>">
                            <div class="flex-shrink-0">
                                <img
                                <?php if( empty(trim($avatar)) ): ?>
                                    src="<?php echo e(URL::asset('build/images/users/user-dummy-img.jpg')); ?>"
                                <?php else: ?>
                                    src="<?php echo e(URL::asset('storage/' .$avatar )); ?>"
                                <?php endif; ?>
                                alt="<?php echo e($name); ?>"
                                class="avatar-xs rounded-circle" />
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fs-13 mb-1"><?php echo e($name); ?></h6>
                                <p class="text-muted mb-0"><?php echo e($companyName); ?></p>
                            </div>
                        </a>
                    </div>
                    <div class="collapse border-top border-top-dashed" id="leadDiscovered<?php echo e($index); ?>">
                        <div class="card-body">
                            <h6 class="fs-14 mb-1">Nesta Technologies <small class="badge bg-danger-subtle text-danger">4 Days</small></h6>
                            <p class="text-muted text-break">As a company grows however, you find it's not as easy to shout across</p>
                            <ul class="list-unstyled vstack gap-2 mb-0">
                                <li>
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 avatar-xxs text-muted">
                                            <i class="ri-question-answer-line"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">Meeting with Thomas</h6>
                                            <small class="text-muted">Yesterday at 9:12AM</small>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 avatar-xxs text-muted">
                                            <i class="ri-mac-line"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">Product Demo</h6>
                                            <small class="text-muted">Monday at 04:41PM</small>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 avatar-xxs text-muted">
                                            <i class="ri-earth-line"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">Marketing Team Meeting</h6>
                                            <small class="text-muted">Monday at 04:41PM</small>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        
                    </div>
                </div>
                <!--end card-->
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <div class="col-sm-12 col-md-4">
        <div class="card">
            <a class="card-body bg-primary-subtle"><!-- data-bs-toggle="collapse" href="#contactInitiated" role="button"
                aria-expanded="false" aria-controls="contactInitiated" -->
                <h5 class="card-title text-uppercase mb-1 fs-14">
                    <?php
                        $columns = array_column($distributedData['audited_by'], 'user_id');
                        $uniqued = count($columns) > 1 ? array_unique($columns) : $columns;
                        echo is_array($uniqued) ? count($uniqued) : 0;
                    ?>
                     Auditores
                </h5>
                <p class="text-muted mb-0"><span class="fw-medium"><?php echo e(is_array($distributedData['audited_by']) ? count($distributedData['audited_by']) : ''); ?> lojas</span></p>
            </a>
        </div>
        <!--end card-->
        <div class="collapse show" id="contactInitiated">
            <?php $__currentLoopData = $distributedData['audited_by']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $userId = $value['user_id'];
                    $companyName = getCompanyAlias($value['company_id']);
                    $avatar = getUserData($userId)['avatar'];
                    $name = getUserData($userId)['name'];
                ?>
                <div class="card mb-1 ribbon-box ribbon-fill ribbon-sm right">
                    <div class="ribbon ribbon-info"><i class="ri-flashlight-fill"></i></div>
                    <div class="card-body">
                        <a class="d-flex align-items-center" data-bs-toggle="collapse" href="#contactInitiated<?php echo e($index); ?>"
                            role="button" aria-expanded="false" aria-controls="contactInitiated<?php echo e($index); ?>">
                            <div class="flex-shrink-0">
                                <img
                                <?php if( empty(trim($avatar)) ): ?>
                                    src="<?php echo e(URL::asset('build/images/users/user-dummy-img.jpg')); ?>"
                                <?php else: ?>
                                    src="<?php echo e(URL::asset('storage/' .$avatar )); ?>"
                                <?php endif; ?>
                                alt="<?php echo e($name); ?>"
                                class="avatar-xs rounded-circle" />
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fs-13 mb-1"><?php echo e($name); ?></h6>
                                <p class="text-muted mb-0"><?php echo e($companyName); ?></p>
                            </div>
                        </a>
                    </div>
                    <div class="collapse border-top border-top-dashed" id="contactInitiated<?php echo e($index); ?>">
                        <div class="card-body">
                            <h6 class="fs-14 mb-1">Nesta Technologies <small class="badge bg-danger-subtle text-danger">4
                                    Days</small></h6>
                            <p class="text-muted text-break">As a company grows however, you find it's not as easy
                                to shout across</p>
                            <ul class="list-unstyled vstack gap-2 mb-0">
                                <li>
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 avatar-xxs text-muted">
                                            <i class="ri-question-answer-line"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">Meeting with Thomas</h6>
                                            <small class="text-muted">Yesterday at 9:12AM</small>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 avatar-xxs text-muted">
                                            <i class="ri-mac-line"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">Product Demo</h6>
                                            <small class="text-muted">Monday at 04:41PM</small>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 avatar-xxs text-muted">
                                            <i class="ri-earth-line"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">Marketing Team Meeting</h6>
                                            <small class="text-muted">Monday at 04:41PM</small>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        
                    </div>
                </div>
                <!--end card-->
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/surveys/components/distributeds-card.blade.php ENDPATH**/ ?>