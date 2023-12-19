<?php
    use App\Models\Survey;
    use App\Models\SurveyAssignments;

    $currentUserId = auth()->id();
?>
<?php if( !empty($data) && is_array($data) ): ?>
    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $assignmentId = intval($assignment['id']);
            $surveyId = intval($assignment['survey_id']);

            $createdAt = $assignment['created_at'];

            $survey = Survey::findOrFail($surveyId);

            $title = $survey->title;

            $recurring = $survey->recurring;

            $deadline = \App\Models\SurveyAssignments::getSurveyAssignmentDeadline($recurring, $createdAt);

            $templateName = getSurveyTemplateNameById($survey->template_id);

            $companyId = intval($assignment['company_id']);
            $companyName = getCompanyNameById($companyId);

            $surveyorId = $assignment['surveyor_id'] ?? null;
            $surveyorStatus = $assignment['surveyor_status'] ?? null;
            $surveyorName = getUserData($surveyorId)['name'] ?? '';
            $surveyorAvatar = getUserData($surveyorId)['avatar'] ?? '';

            $auditorId = $assignment['auditor_id'] ?? null;
            $auditorStatus = $assignment['auditor_status'] ?? null;
            $auditorName = $auditorId ? getUserData($auditorId)['name'] : '';
            $auditorAvatar = $auditorId ? getUserData($auditorId)['avatar'] : '';

            $labelTitle = SurveyAssignments::getSurveyAssignmentLabelTitle($surveyorStatus, $auditorStatus, $statusKey);

            if($designated == 'auditor'){
                $designatedUserId = $auditorId;
            }elseif($designated == 'surveyor'){
                $designatedUserId = $surveyorId;
            }

            $percentage = SurveyAssignments::calculateSurveyPercentage($surveyId, $companyId, $assignmentId, $surveyorId, $auditorId, $designated);
            $progressBarClass = getProgressBarClass($percentage);
        ?>

        <div class="card tasks-box bg-body" data-assignment-id="<?php echo e($assignmentId); ?>">
            <div class="card-body">
                <div class="row mb-0">
                    <div class="col text-theme fw-medium fs-15">
                        <?php echo e($companyName); ?>

                    </div>
                    <div class="col-auto">
                        <?php if( $surveyorStatus == 'completed' && $auditorStatus == 'completed'): ?>
                            <span class="badge bg-success-subtle text-success badge-border" data-bs-toggle="tooltip" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" title="<?php echo e($labelTitle); ?>">
                                <span class="text-info">Vistoria</span> <span class="text-body">+</span> <span class="text-secondary">Auditoria</span>
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

                <span class="fs-12" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="A data limite para execução desta tarefa">
                    Prazo: <?php echo e($deadline); ?>

                </span>

                <h5 class="fs-12 text-truncate task-title mb-0 mt-2">
                    <?php echo e($title); ?>

                </h5>

                <?php if(in_array($statusKey, ['losted'])): ?>
                    <?php if( $surveyorStatus == 'losted' && $auditorStatus == 'losted' ): ?>
                        <div class="text-danger small mt-2">
                            Esta <u>Auditoria</u> foi perdida pois a <u>Vistoria</u> não foi efetuada na data prevista.
                        </div>
                    <?php elseif( $surveyorStatus == 'completed' && $auditorStatus == 'losted' ): ?>
                        <div class="text-warning small mt-2">
                            A <u>Vistoria</u> foi completada. Entretanto, a <u>Auditoria</u> não foi concluída em tempo.
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
                                    <img src="<?php echo e($surveyorAvatar); ?>"
                                    alt="<?php echo e($surveyorName); ?>" class="rounded-circle avatar-xxs border border-1 border-white" loading="lazy">
                                </a>
                            <?php else: ?>
                                <a href="<?php echo e(route('profileShowURL', $surveyorId)); ?>" class="d-inline-block me-1" data-bs-toggle="tooltip" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" title="Tarefa de Vistoria delegada a <u><?php echo e($surveyorName); ?></u>">
                                    <img src="<?php echo e($surveyorAvatar); ?>"
                                    alt="<?php echo e($surveyorName); ?>" class="rounded-circle avatar-xxs border border-1 border-white" loading="lazy">
                                </a>

                                <?php if($auditorId): ?>
                                    <a href="<?php echo e(route('profileShowURL', $auditorId)); ?>" class="d-inline-block ms-1" data-bs-toggle="tooltip" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" title="Tarefa de Auditoria delegada a <u><?php echo e($auditorName); ?></u>">
                                        <img src="<?php echo e($auditorAvatar); ?>"
                                        alt="<?php echo e($auditorName); ?>" class="rounded-circle avatar-xxs border border-1 border-secondary" loading="lazy">
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-auto text-end">
                        
                        <?php if($currentUserId == $designatedUserId && in_array($statusKey, ['new','pending','in_progress']) ): ?>
                            <button type="button"
                                data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top"
                                title="<?php echo e($status['reverse']); ?>"
                                class="btn btn-sm btn-label right waves-effect btn-soft-<?php echo e($status['color']); ?> <?php echo e($designated == 'surveyor' ? 'btn-assignment-surveyor-action' : 'btn-assignment-auditor-action'); ?>"
                                data-survey-id="<?php echo e($surveyId); ?>"
                                data-assignment-id="<?php echo e($assignmentId); ?>"
                                data-current-status="<?php echo e($statusKey); ?>">
                                    <i class="<?php echo e($status['icon']); ?> label-icon align-middle fs-16"></i> <?php echo e($status['reverse']); ?>

                            </button>

                            <?php if( in_array('audit', $currentUserCapabilities) && in_array($statusKey, ['new','pending','in_progress','completed']) ): ?>
                                <a href="<?php echo e(route('assignmentShowURL', $assignmentId)); ?>"
                                    data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top"
                                    title="Visualizar"
                                    class="btn btn-sm waves-effect btn-soft-dark ri-eye-line">
                                </a>
                            <?php endif; ?>
                        <?php elseif( ( ( $currentUserId === $surveyorId || $currentUserId === $auditorId ) && in_array($statusKey, ['completed']) ) || in_array('audit', $currentUserCapabilities) ): ?>
                            <a href="<?php echo e(route('assignmentShowURL', $assignmentId)); ?>"
                                data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top"
                                title="Visualizar"
                                class="btn btn-sm btn-label right waves-effect btn-soft-dark">
                                    <i class="ri-eye-line label-icon align-middle"></i> Visualizar
                            </a>
                        <?php endif; ?>

                        

                        <?php if( $currentUserId === $designatedUserId && $designated === 'surveyor' && $surveyorId === $auditorId && in_array($statusKey, ['auditing']) ): ?>
                            <i class="text-theme ri-questionnaire-fill" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top" title="Neste contexto a você foram delegadas tarefas de Vistoria e Auditoria.<br>Procure na coluna <b>Nova</b> o card correspondente a <b><?php echo e($companyName); ?></b> de prazo: <b><?php echo e($deadline); ?></b> e inicialize a tarefa "></i>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!--end card-body-->
            <?php if( in_array($statusKey, ['in_progress']) ): ?>
                <div class="progress progress-sm animated-progress custom-progress p-0 rounded-bottom-2" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="<?php echo e($percentage); ?>%">
                    <div class="progress-bar bg-<?php echo e($progressBarClass); ?> rounded-0" role="progressbar" style="width: <?php echo e($percentage); ?>%" aria-valuenow="<?php echo e($percentage); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/surveys/layouts/profile-task-card.blade.php ENDPATH**/ ?>