<?php
    use App\Models\Survey;

    $currentUserId = auth()->id();
?>
<?php if( !empty($data) && is_array($data) ): ?>
    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $assignmentId = intval($assignment['id']);
            $surveyId = intval($assignment['survey_id']);

            $survey = Survey::findOrFail($surveyId);
            $templateName = getTemplateNameById($survey->template_id);

            $companyId = intval($assignment['company_id']);
            $companyName = getCompanyNameById($companyId);

            $surveyorId = $assignment['surveyor_id'] ?? null;
            $surveyorStatus = $assignment['surveyor_status'] ?? null;
            $surveyorName = getUserData($surveyorId)['name'];
            $surveyorAvatar = getUserData($surveyorId)['avatar'];

            $auditorId = $assignment['auditor_id'] ?? null;
            $auditorStatus = $assignment['auditor_status'] ?? null;
            $auditorName = getUserData($auditorId)['name'];
            $auditorAvatar = getUserData($auditorId)['avatar'];

            $dateTitle = getDateTitle($assignment['created_at'], $statusKey); // Assume this function exists

            $labelTitle = getLabelTitle($surveyorStatus, $auditorStatus, $statusKey); // Assume this function exists

            if($designated == 'auditor'){
                $designatedUserId = $auditorId;
            }elseif($designated == 'surveyor'){
                $designatedUserId = $surveyorId;
            }

            $percentage = calculatePercentage($surveyId, $companyId, $assignmentId, $surveyorId, $auditorId, $designated); // Assume this function exists
            $progressBarClass = getProgressBarClass($percentage); // Assume this function exists
        ?>
        
        <div class="card tasks-box bg-body" data-assignment-id="<?php echo e($assignmentId); ?>">
            <div class="card-body">
                <div class="row mb-0">
                    <div class="col text-theme fw-medium fs-15">
                        <?php echo e($companyName); ?>

                    </div>
                    <div class="col-auto">
                        <?php if($designated == 'auditor'): ?>
                            <span class="badge bg-dark-subtle text-secondary badge-border" data-bs-toggle="tooltip" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" title="<?php echo e($labelTitle); ?>">
                                Auditoria
                                <?php if( in_array($statusKey, ['completed']) && $surveyorStatus == 'completed' && $auditorStatus == 'completed' ): ?>
                                    <i class="ri-check-double-fill ms-2 text-success"></i>
                                <?php endif; ?>
                            </span>
                        <?php elseif($designated == 'surveyor'): ?>
                            <span class="badge bg-dark-subtle text-body badge-border" data-bs-toggle="tooltip" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" title="<?php echo e($labelTitle); ?>">
                                Vistoria
                                <?php if( in_array($statusKey, ['completed']) && $surveyorStatus == 'completed' && $auditorStatus == 'completed' ): ?>
                                    <i class="ri-check-double-fill ms-2 text-success"></i>
                                <?php endif; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <span data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="<?php echo e($dateTitle); ?>">
                    <?php echo e($assignment['created_at'] ? date("d/m/Y", strtotime($assignment['created_at'])) : '-'); ?>

                </span>
                <h5 class="fs-13 text-truncate task-title mb-0 mt-2">
                    <?php echo e($templateName); ?>

                </h5>
                <?php if(in_array($statusKey, ['losted'])): ?>
                    <?php if( $surveyorStatus == 'losted' && $auditorStatus == 'losted' ): ?>
                        <div class="text-danger small mt-2">
                            Esta <u>Auditoria</u> foi perdida pois a <u>Vistoria</u> não foi efetuada na data prevista.
                        </div>
                    <?php elseif( $surveyorStatus == 'completed' && $auditorStatus == 'losted' ): ?>
                        <div class="text-warning small mt-2">
                            A <u>Vistoria</u> foi completada. Entretanto, a <u>Auditoria</u> não foi efetuada na data prevista.
                        </div>
                    <?php elseif( $surveyorStatus != 'completed' && $surveyorStatus != 'losted' && $auditorStatus == 'losted' ): ?>
                        <div class="text-warning small mt-2">
                            Esta tarefa foi perdida pois não foi efetuada na data prevista.
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <!--end card-body-->
            <div class="card-footer border-top-dashed bg-body">
                <div class="row">
                    <div class="col small">
                        <div class="avatar-group ps-0">
                            <?php if($surveyorId === $auditorId): ?>
                                <a href="<?php echo e(route('profileShowURL', $surveyorId)); ?>" class="d-inline-block me-1" data-bs-toggle="tooltip" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" title="Tarefas de Vistoria e Auditoria delegadas a <u><?php echo e($surveyorName); ?></u>">
                                    <img
                                    <?php if( empty(trim($surveyorAvatar)) ): ?>
                                        src="<?php echo e(URL::asset('build/images/users/user-dummy-img.jpg')); ?>"
                                    <?php else: ?>
                                        src="<?php echo e($surveyorAvatar); ?>"
                                    <?php endif; ?>
                                    alt="<?php echo e($surveyorName); ?>" class="rounded-circle avatar-xxs">
                                </a>
                            <?php else: ?>
                                <a href="<?php echo e(route('profileShowURL', $surveyorId)); ?>" class="d-inline-block me-1" data-bs-toggle="tooltip" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" title="Tarefa de Vistoria delegada a <u><?php echo e($surveyorName); ?></u>">
                                    <img
                                    <?php if( empty(trim($surveyorAvatar)) ): ?>
                                        src="<?php echo e(URL::asset('build/images/users/user-dummy-img.jpg')); ?>"
                                    <?php else: ?>
                                        src="<?php echo e($surveyorAvatar); ?>"
                                    <?php endif; ?>
                                    alt="<?php echo e($surveyorName); ?>" class="rounded-circle avatar-xxs">
                                </a>

                                <a href="<?php echo e(route('profileShowURL', $auditorId)); ?>" class="d-inline-block ms-2" data-bs-toggle="tooltip" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" title="Tarefa de Auditoria delegada a <u><?php echo e($auditorName); ?></u>">
                                    <img
                                    <?php if( empty(trim($auditorAvatar)) ): ?>
                                        src="<?php echo e(URL::asset('build/images/users/user-dummy-img.jpg')); ?>"
                                    <?php else: ?>
                                        src="<?php echo e($auditorAvatar); ?>"
                                    <?php endif; ?>
                                    alt="<?php echo e($auditorName); ?>" class="rounded-circle avatar-xxs">
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <?php if($currentUserId === $designatedUserId && in_array($statusKey, ['new','pending','in_progress']) ): ?>
                            <button type="button"
                                title="<?php echo e($status['reverse']); ?>"
                                class="btn btn-sm btn-label right waves-effect btn-soft-<?php echo e($status['color']); ?> <?php echo e($designated == 'surveyor' ? 'btn-assignment-surveyor-action' : 'btn-assignment-auditor-action'); ?>"
                                data-survey-id="<?php echo e($surveyId); ?>"
                                data-assignment-id="<?php echo e($assignmentId); ?>"
                                data-current-status="<?php echo e($statusKey); ?>">
                                    <i class="<?php echo e($status['icon']); ?> label-icon align-middle fs-16"></i> <?php echo e($status['reverse']); ?>

                            </button>
                        <?php elseif( ( $currentUserId === $surveyorId || $currentUserId === $auditorId ) && in_array($statusKey, ['completed']) ): ?>
                            <a href="<?php echo e(route('assignmentShowURL', $assignmentId)); ?>"
                                title="Visualizar"
                                class="btn btn-sm btn-label right waves-effect btn-soft-success">
                                    <i class="ri-eye-line label-icon align-middle fs-16"></i> Visualizar
                            </a>
                        <?php endif; ?>

                        <?php if( $surveyorStatus == 'completed' && $auditorStatus == 'losted' && in_array($statusKey, ['losted']) ): ?>
                            <a href="<?php echo e(route('assignmentShowURL', $assignmentId)); ?>"
                                title="Visualizar"
                                class="btn btn-sm btn-label right waves-effect btn-soft-dark">
                                    <i class="ri-eye-line label-icon align-middle fs-16"></i> Visualizar
                            </a>
                        <?php endif; ?>

                        <?php if( $currentUserId === $designatedUserId && $designated === 'surveyor' && $surveyorId === $auditorId && in_array($statusKey, ['auditing']) ): ?>
                            <i class="text-theme ri-questionnaire-fill" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top" title="Neste contexto a você foram delegadas tarefas de Vistoria e Auditoria.<br>Procure na coluna <b>Nova</b> o card correspondente a <b><?php echo e($companyName); ?></b> de <b><?php echo e($assignment['created_at'] ? date("d/m/Y", strtotime($assignment['created_at'])) : '-'); ?></b> e inicialize a tarefa "></i>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!--end card-body-->
            <?php if( in_array($statusKey, ['in_progress']) || ( in_array($statusKey, ['auditing']) && $designated == 'surveyor' ) ): ?>
                <div class="progress progress-sm animated-progress custom-progress" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="<?php echo e($percentage); ?>%">
                    <div class="progress-bar bg-<?php echo e($progressBarClass); ?>" role="progressbar" style="width: <?php echo e($percentage); ?>%" aria-valuenow="<?php echo e($percentage); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/surveys/layouts/profile-task-card.blade.php ENDPATH**/ ?>