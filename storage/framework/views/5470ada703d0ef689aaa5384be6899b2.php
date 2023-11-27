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

            $surveyorStatus = $assignment['surveyor_status'];
            $auditorStatus = $assignment['auditor_status'];

            $surveyorAvatar = getUserData($surveyorId)['avatar'];
            $surveyorName = getUserData($surveyorId)['name'];

            $auditorAvatar = getUserData($auditorId)['avatar'];
            $auditorName = getUserData($auditorId)['name'];

            $survey = Survey::findOrFail($surveyId);
            $templateName = getTemplateNameById($survey->template_id);

            // Count the number of steps that have been finished
            $countTopics = countSurveyTopics($surveyId);
            $countSurveyorResponses = countSurveySurveyorResponses($surveyorId, $surveyId, $companyId);
            $countAuditorResponses = countSurveyAuditorResponses($auditorId, $surveyId, $companyId);

            // Calculate the percentage
            $percentage = 0;
            if ($countTopics > 0) {
                $percentage = ($countSurveyorResponses / $countTopics) * 100;
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
            <div class="card tasks-box bg-body">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col text-theme fw-medium fs-15">
                            <?php echo e(getCompanyNameById($companyId)); ?> (assId: <?php echo e($assignmentId); ?>)
                        </div>
                        <div class="col-auto">
                            <?php if($statusKey == 'waiting'): ?>
                                <span class="badge bg-dark-subtle text-secondary badge-border">Auditoria</span>
                            <?php else: ?>
                                <?php if( $currentUserId == $surveyorId && $surveyorStatus != 'auditing' ): ?>
                                    <span class="badge bg-dark-subtle text-body badge-border">Vistoria</span>
                                <?php elseif( $currentUserId == $auditorId && $surveyorStatus == 'auditing' ): ?>
                                    <span class="badge bg-dark-subtle text-secondary badge-border">Auditoria</span>
                                <?php elseif( $currentUserId == $surveyorId && $currentUserId == $auditorId ): ?>
                                    <span class="badge bg-dark-subtle text-secondary badge-border">Auditoria</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <h5 class="fs-13 text-truncate task-title mb-0">
                        <?php echo e($templateName); ?>

                    </h5>
                </div>
                <!--end card-body-->
                <div class="card-footer border-top-dashed bg-body">
                    <div class="row">
                        <div class="col small">
                            <span data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="A data em que esta tarefa deverá ser desempenhada">
                                <i class="ri-time-line align-bottom"></i> <?php echo e($assignment['created_at'] ? date("d/m/Y", strtotime($assignment['created_at'])) : '-'); ?>

                            </span>
                        </div>
                        <div class="col-auto">
                            <?php if($currentUserId == $surveyorId || $currentUserId == $auditorId): ?>
                                <?php if( in_array($statusKey, ['new','pending','in_progress'])): ?>
                                    <button type="button"
                                    data-bs-toggle="tooltip"
                                    data-bs-trigger="hover"
                                    data-bs-placement="top"
                                    title="<?php echo e($status['button']); ?>"
                                    class="btn btn-sm btn-label right waves-effect btn-soft-<?php echo e($status['color']); ?> btn-assignment-action"
                                    data-survey-id="<?php echo e($surveyId); ?>"
                                    data-assignment-id="<?php echo e($assignmentId); ?>"
                                    data-current-status="<?php echo e($statusKey); ?>">
                                        <i class="<?php echo e($status['icon']); ?> label-icon align-middle fs-16"></i> <?php echo e($status['button']); ?>

                                    </button>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="avatar-group">
                                    <span class="avatar-group-item">
                                        <a href="<?php echo e(route('profileShowURL', $surveyorId)); ?>" class="d-inline-block" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Tarefa de Vistoria delegada a <?php echo e($surveyorName); ?>">
                                            <img
                                            <?php if( empty(trim($surveyorAvatar)) ): ?>
                                                src="<?php echo e(URL::asset('build/images/users/user-dummy-img.jpg')); ?>"
                                            <?php else: ?>
                                                src="<?php echo e(URL::asset('storage/' .$surveyorAvatar )); ?>"
                                            <?php endif; ?>
                                            alt="<?php echo e($surveyorName); ?>" class="rounded-circle avatar-xxs">
                                        </a>
                                    </span>
                                    <span class="avatar-group-item">
                                        <a href="<?php echo e(route('profileShowURL', $auditorId)); ?>" class="d-inline-block" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Tarefa de Auditoria delegada a <?php echo e($auditorName); ?>">
                                            <img
                                            <?php if( empty(trim($auditorAvatar)) ): ?>
                                                src="<?php echo e(URL::asset('build/images/users/user-dummy-img.jpg')); ?>"
                                            <?php else: ?>
                                                src="<?php echo e(URL::asset('storage/' .$auditorAvatar )); ?>"
                                            <?php endif; ?>
                                            alt="<?php echo e($auditorName); ?>" class="rounded-circle avatar-xxs">
                                        </a>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <!--end card-body-->
                <?php if( in_array($statusKey, ['in_progress'])): ?>
                    <div class="progress progress-sm" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="<?php echo e($percentage); ?>%">
                        <div class="progress-bar bg-<?php echo e($progressBarClass); ?>" role="progressbar" style="width: <?php echo e($percentage); ?>%" aria-valuenow="<?php echo e($percentage); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                <?php endif; ?>
            </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/surveys/layouts/profile-tasks-box.blade.php ENDPATH**/ ?>