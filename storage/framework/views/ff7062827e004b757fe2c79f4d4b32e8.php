<?php
    use App\Models\Survey;

    $currentUserId = auth()->id();
?>
<?php if( !empty($data) && is_array($data) ): ?>
    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $assignmentId = intval($assignment['id']);
            $surveyId = intval($assignment['survey_id']);
            $companyId = intval($assignment['company_id']);

            $surveyorId = isset($assignment['surveyor_id']) ? intval($assignment['surveyor_id']) : null;
            $auditorId = isset($assignment['auditor_id']) ? intval($assignment['auditor_id']) : null;

            $surveyorStatus = $assignment['surveyor_status'] ?? null;
            $auditorStatus = $assignment['auditor_status'] ?? null;

            if($designated == 'auditor'){
                $designatedUserId = $auditorId;
            }elseif($designated == 'surveyor'){
                $designatedUserId = $surveyorId;
            }

            $surveyorAvatar = getUserData($surveyorId)['avatar'];
            $surveyorName = getUserData($surveyorId)['name'];

            $auditorAvatar = getUserData($auditorId)['avatar'];
            $auditorName = getUserData($auditorId)['name'];

            $survey = Survey::findOrFail($surveyId);
            $templateName = getTemplateNameById($survey->template_id);

            // Count the number of steps that have been finished
            $countTopics = countSurveyTopics($surveyId);

            $countResponses = 0;

            if( in_array($statusKey, ['auditing']) && $designated == 'surveyor' ){
                $countResponses = countSurveyAuditorResponses($auditorId, $surveyId, $companyId);
            }else{
                if($designated == 'auditor'){
                    $countResponses = countSurveyAuditorResponses($auditorId, $surveyId, $companyId);
                }elseif($designated == 'surveyor'){
                    $countResponses = countSurveySurveyorResponses($surveyorId, $surveyId, $companyId);
                }
            }

            // Calculate the percentage
            $percentage = 0;
            if ($countTopics > 0) {
                $percentage = ($countResponses / $countTopics) * 100;
            }

            // Determine the progress bar class based on the percentage
            $progressBarClass = 'danger'; // default class
            if ($percentage > 25) {
                $progressBarClass = 'warning';
            }
            if ($percentage > 50) {
                $progressBarClass = 'primary';
            }
            if ($percentage > 75) {
                $progressBarClass = 'info';
            }
            if ($percentage > 95) {
                $progressBarClass = 'secondary';
            }
            if ($percentage >= 100) {
                $progressBarClass = 'success';
            }
        ?>
        <div class="card tasks-box bg-body" data-assignment-id="<?php echo e($assignmentId); ?>">
            <div class="card-body">
                <div class="row mb-0">
                    <div class="col text-theme fw-medium fs-15">
                        <?php echo e(getCompanyNameById($companyId)); ?>

                    </div>
                    <div class="col-auto">
                        <?php if($designated == 'auditor'): ?>
                            <span class="badge bg-dark-subtle text-secondary badge-border">Auditoria</span>
                        <?php elseif($designated == 'surveyor'): ?>
                            <span class="badge bg-dark-subtle text-body badge-border">Vistoria</span>
                        <?php endif; ?>
                    </div>
                </div>
                <span data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="A data em que esta tarefa deverá ser desempenhada">
                    <?php echo e($assignment['created_at'] ? date("d/m/Y", strtotime($assignment['created_at'])) : '-'); ?>

                </span>
                <h5 class="fs-13 text-truncate task-title mb-0 mt-2">
                    <?php echo e($templateName); ?>

                </h5>
                <?php if( $designated == 'auditor' && $surveyorStatus == 'losted' && $auditorStatus == 'losted' ): ?>
                    <div class="text-danger small mt-2">Esta <u>Auditoria</u> foi perdida pois a <u>Vistoria</u> não foi efetuada na data prevista</div>
                <?php endif; ?>

                <?php if( $designated == 'surveyor' && $surveyorStatus == 'losted' ): ?>
                    <div class="text-danger small mt-2">Esta <u>Vistoria</u> foi perdida pois não foi efetuada na data prevista</div>
                <?php endif; ?>

                <?php if( $surveyorStatus == 'completed' && $auditorStatus == 'completed' ): ?>
                    <div class="text-success small mt-2"><u>Vistoria</u> e <u>Auditoria</u> concluídas</div>
                <?php endif; ?>
            </div>
            <!--end card-body-->
            <div class="card-footer border-top-dashed bg-body">
                <div class="row">
                    <div class="col small">
                        <div class="avatar-group ps-0">
                            <?php if($surveyorId === $auditorId): ?>
                                <!--
                                    href="javascript:void(0);" onclick="alert('Message feature under development');"
                                    <br>Clique para enviar uma mensagem.
                                -->
                                <a href="<?php echo e(route('profileShowURL', $surveyorId)); ?>" class="d-inline-block me-1" data-bs-toggle="tooltip" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" title="Tarefas de Vistoria e Auditoria delegadas a <u><?php echo e($surveyorName); ?></u>">
                                    <img
                                    <?php if( empty(trim($surveyorAvatar)) ): ?>
                                        src="<?php echo e(URL::asset('build/images/users/user-dummy-img.jpg')); ?>"
                                    <?php else: ?>
                                        src="<?php echo e(URL::asset('storage/' .$surveyorAvatar )); ?>"
                                    <?php endif; ?>
                                    alt="<?php echo e($surveyorName); ?>" class="rounded-circle avatar-xxs">
                                </a>
                            <?php else: ?>
                                <!--
                                    href="javascript:void(0);" onclick="alert('Message feature under development');"
                                    <br>Clique para enviar uma mensagem.
                                -->
                                <a href="<?php echo e(route('profileShowURL', $surveyorId)); ?>" class="d-inline-block me-1" data-bs-toggle="tooltip" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" title="Tarefa de Vistoria delegada a <u><?php echo e($surveyorName); ?></u>">
                                    <img
                                    <?php if( empty(trim($surveyorAvatar)) ): ?>
                                        src="<?php echo e(URL::asset('build/images/users/user-dummy-img.jpg')); ?>"
                                    <?php else: ?>
                                        src="<?php echo e(URL::asset('storage/' .$surveyorAvatar )); ?>"
                                    <?php endif; ?>
                                    alt="<?php echo e($surveyorName); ?>" class="rounded-circle avatar-xxs">
                                </a>

                                <!--
                                    href="javascript:void(0);" onclick="alert('Message feature under development');"
                                    <br>Clique para enviar uma mensagem.
                                -->
                                <a href="<?php echo e(route('profileShowURL', $auditorId)); ?>" class="d-inline-block ms-2" data-bs-toggle="tooltip" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" title="Tarefa de Auditoria delegada a <u><?php echo e($auditorName); ?></u>">
                                    <img
                                    <?php if( empty(trim($auditorAvatar)) ): ?>
                                        src="<?php echo e(URL::asset('build/images/users/user-dummy-img.jpg')); ?>"
                                    <?php else: ?>
                                        src="<?php echo e(URL::asset('storage/' .$auditorAvatar )); ?>"
                                    <?php endif; ?>
                                    alt="<?php echo e($auditorName); ?>" class="rounded-circle avatar-xxs">
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <?php if($currentUserId == $designatedUserId && in_array($statusKey, ['new','pending','in_progress']) ): ?>
                            <button type="button"
                                data-bs-toggle="tooltip"
                                data-bs-trigger="hover"
                                data-bs-placement="top"
                                title="<?php echo e($status['reverse']); ?>"
                                class="btn btn-sm btn-label right waves-effect btn-soft-<?php echo e($status['color']); ?> <?php echo e($designated == 'surveyor' ? 'btn-assignment-surveyor-action' : 'btn-assignment-auditor-action'); ?>"
                                data-survey-id="<?php echo e($surveyId); ?>"
                                data-assignment-id="<?php echo e($assignmentId); ?>"
                                data-current-status="<?php echo e($statusKey); ?>">
                                <i class="<?php echo e($status['icon']); ?> label-icon align-middle fs-16"></i> <?php echo e($status['reverse']); ?>

                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!--end card-body-->
            <?php if( in_array($statusKey, ['in_progress']) || ( in_array($statusKey, ['auditing']) && $designated == 'surveyor' ) ): ?>
                <div class="progress progress-sm" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="<?php echo e($percentage); ?>%">
                    <div class="progress-bar bg-<?php echo e($progressBarClass); ?>" role="progressbar" style="width: <?php echo e($percentage); ?>%" aria-valuenow="<?php echo e($percentage); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/surveys/layouts/profile-task-card.blade.php ENDPATH**/ ?>