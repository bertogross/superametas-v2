<?php
    $assignmentId = $assignmentData->id;
    $assignmentCreatedAt = $assignmentData->created_at;
    $surveyId = $assignmentData->survey_id;
    $companyId = $assignmentData->company_id;

    $now = \Carbon\Carbon::now()->startOfDay();
    $timeLimit = $assignmentCreatedAt->endOfDay();

    $surveyorId = $assignmentData->surveyor_id;
    $getSurveyorUserData = getUserData($surveyorId);
    $surveyorName = $getSurveyorUserData['name'] ?? '';
    $surveyorAvatar = $getSurveyorUserData['avatar'];

    $auditorId = $assignmentData->auditor_id;
    $getAuditorUserData = getUserData($auditorId);
    $auditorName = $auditorId ? $getAuditorUserData['name'] : '';
    $auditorAvatar = $auditorId ? $getAuditorUserData['avatar'] : '';

    $surveyorStatus = $assignmentData->surveyor_status;
    $auditorStatus = $assignmentData->auditor_status;

    $title = $surveyData->title;
    $recurring = $surveyData->recurring;

    $templateName = $surveyData ? getSurveyTemplateNameById($surveyData->template_id) : '';
    $templateDescription = $surveyData ? getTemplateDescriptionById($surveyData->template_id) : '';

    $companyName = $companyId ? getCompanyNameById($companyId) : '';


    $countSurveyAssignmentBySurveyId = \App\Models\SurveyAssignments::countSurveyAssignmentBySurveyId($surveyId);

    $responsesData = \App\Models\SurveyResponse::where('assignment_id', $assignmentId)
        ->get()
        ->toArray();

    $complianceSurveyorYesCount = $complianceSurveyorNoCount = $complianceAuditorYesCount = $complianceAuditorNoCount = 0;

    foreach ($responsesData as $item) {
        if (isset($item['compliance_survey'])) {
            if ($item['compliance_survey'] === 'yes') {
                $complianceSurveyorYesCount++;
            } elseif ($item['compliance_survey'] === 'no') {
                $complianceSurveyorNoCount++;
            }
        }
        if (isset($item['compliance_audit'])) {
            if ($item['compliance_audit'] === 'yes') {
                $complianceAuditorYesCount++;
            } elseif ($item['compliance_audit'] === 'no') {
                $complianceAuditorNoCount++;
            }
        }
    }

    $countTopics = \App\Models\SurveyTopic::countSurveyTopics($surveyId);

    $countResponses = \App\Models\SurveyResponse::countSurveySurveyorResponses($surveyorId, $surveyId, $assignmentId);

    $percentage = $countResponses > 0 ? ($countResponses / $countTopics) * 100 : 0;
    $percentage = number_format($percentage, 0);

?>

<?php $__env->startSection('title'); ?>
    Resultado da Checklist
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(URL::asset('build/libs/glightbox/css/glightbox.min.css')); ?>">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

    <div id="content">

        <div class="card mt-n4 mx-n3">
            <div class="bg-warning-subtle">
                <div class="card-body pb-4">
                    <h4 class="fw-semibold">
                        <span class="text-theme"><?php echo e($companyName); ?></span> <i class="ri-arrow-right-s-fill align-bottom"></i> <?php echo e(limitChars($title ?? '', 100)); ?>

                    </h4>
                    <div class="hstack gap-3 flex-wrap">

                        <div class="text-muted" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="left" title="A data limite para realizar esta tarefa">
                            Prazo: <?php echo e($assignmentCreatedAt ? \Carbon\Carbon::parse($assignmentCreatedAt)->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY') : '-'); ?>

                        </div>

                        <div class="vr"></div>

                        <div class="text-muted">
                            Vistoria: <a href="<?php echo e(route('profileShowURL', $surveyorId)); ?>" class="text-muted" title="Acessar Perfil"><?php echo e($surveyorName); ?></a>
                        </div>

                        <div class="vr"></div>

                        <div class="text-muted">
                            Auditoria: <a href="<?php echo e(route('profileShowURL', $auditorId)); ?>" class="text-muted" title="Acessar Perfil"><?php echo e($auditorName); ?></a>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div>
        </div><!-- end card -->

        <?php if( $recurring != 'once' && $countSurveyAssignmentBySurveyId > 0 ): ?>
            <a href="<?php echo e(route('surveysShowURL', $surveyId)); ?>" class="btn btn-lg btn-soft-theme float-end position-relative" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Visualização Analítica em Checklists Recorrentes">
                <i class="ri-line-chart-fill"></i> <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary"><?php echo e($countSurveyAssignmentBySurveyId); ?> <span class="visually-hidden">registros</span></span>
            </a>
        <?php endif; ?>

        <?php if($templateDescription): ?>
            <h6 class="text-uppercase mb-3">Descrição da Tarefa</h6>
            <p class="text-muted">
                <?php echo nl2br($templateDescription); ?>

            </p>
        <?php endif; ?>

        <?php if($surveyorStatus == 'new'): ?>
            <div class="alert alert-info alert-dismissible alert-label-icon label-arrow fade show" role="alert">
                <i class="ri-alert-line label-icon blink"></i> Esta tarefa ainda não foi inicializada
            </div>
        <?php elseif($surveyorStatus == 'in_progress'): ?>
            <div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show" role="alert">
                <i class="ri-alert-line label-icon"></i> Esta tarefa está sendo executada por <a href="<?php echo e(route('profileShowURL', $surveyorId)); ?>" title="Acessar Perfil"><?php echo e($surveyorName); ?></a>.<br>
                Esta sessão irá recarregar a cada 60 segundos.
            </div>
        <?php elseif( $surveyorStatus == 'losted' && !$countResponses  ): ?>
            <div class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show" role="alert">
                <i class="ri-alert-line label-icon blink"></i> Tarefa de Vistoria perdida por não ter sido concluída no prazo
            </div>
        <?php elseif($surveyorStatus == 'losted' && $countResponses): ?>
            <div class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show" role="alert">
                <i class="ri-alert-line label-icon blink"></i> Tarefa de Vistoria perdida por não ter sido concluída no prazo. Mas, alguns dados foram capturados.
            </div>
        

        

        <?php endif; ?>

        <?php if($countResponses ): ?>
            <div class="row mb-2 mt-4">
                <div class="col-sm-6 col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div id="barTermsChart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div id="mixedTermsChart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4">
                    <div class="row">
                        <div class="col">
                            <div class="card">
                                <div class="card-body" style="height: 145px;">
                                    <img
                                    <?php if( empty(trim($surveyorAvatar)) ): ?>
                                        src="<?php echo e(URL::asset('build/images/users/user-dummy-img.jpg')); ?>"
                                    <?php else: ?>
                                        src="<?php echo e($surveyorAvatar); ?>"
                                    <?php endif; ?>
                                    alt="<?php echo e($surveyorName); ?>"
                                    class="avatar-xs rounded-circle float-end" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top" title="Vistoria realizada por <?php echo e($surveyorName); ?>" />
                                    <h6 class="text-muted text-uppercase mb-4">Vistoria</h6>
                                    <span class="text-success">Conforme</span>: <?php echo e($complianceSurveyorYesCount); ?>

                                    <br><br>
                                    <span class="text-danger">Não Conforme</span>: <?php echo e($complianceSurveyorNoCount); ?>

                                </div>
                            </div>
                        </div>
                        <div class="col ">
                            <div class="card">
                                <div class="card-body" style="height: 145px;">
                                    <?php if($auditorStatus == 'losted'): ?>
                                        <span class="fs-5 float-end ri-alert-fill text-warning" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="left"  title="Auditoria não foi realizada"></span>
                                    <?php elseif($timeLimit->gt($now) && $auditorStatus != 'completed'): ?>
                                        <span class="fs-5 float-end ri-time-line text-secondary blink" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="left"  title="Dentro do prazo para realizar Auditoria"></span>
                                    <?php else: ?>
                                        <img
                                        <?php if( empty(trim($auditorAvatar)) ): ?>
                                            src="<?php echo e(URL::asset('build/images/users/user-dummy-img.jpg')); ?>"
                                        <?php else: ?>
                                            src="<?php echo e($auditorAvatar); ?>"
                                        <?php endif; ?>
                                        alt="<?php echo e($auditorName); ?>"
                                        class="avatar-xs rounded-circle float-end" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top" title="Auditoria realizada por <?php echo e($auditorName); ?>" />
                                    <?php endif; ?>

                                    <h6 class="text-muted text-uppercase mb-4">Auditoria</h6>

                                    <?php if( !$complianceAuditorYesCount && !$complianceAuditorNoCount ): ?>
                                        <?php if( in_array('audit', $currentUserCapabilities) && in_array($surveyorStatus, ['new','pending','in_progress','completed']) && $timeLimit->gt($now) ): ?>
                                            <button type="button"
                                            data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top"
                                            title="Requisitar esta tarefa de Auditoria"
                                            class="btn btn-label right waves-effect btn-soft-secondary btn-assignment-audit-enter w-100"
                                            data-assignment-id="<?php echo e($assignmentId); ?>">
                                                <i class="ri-fingerprint-2-line label-icon align-middle fs-16"></i> Auditar
                                            </button>

                                            <div class="form-text mt-2" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="left" title="A data limite para realizar esta tarefa">
                                                <?php echo e($assignmentCreatedAt ? 'Prazo: ' . \Carbon\Carbon::parse($assignmentCreatedAt)->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY') : ''); ?>

                                            </div>
                                        <?php elseif( in_array('audit', $currentUserCapabilities) && $auditorId == auth()->id() && $timeLimit->gt($now) ): ?>
                                            <div class="row mb-3">
                                                <div class="col-6 pe-1">
                                                    <button type="button"
                                                    data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top"
                                                    title="Revogar esta tarefa de Auditoria"
                                                    class="btn btn-sm btn-label right waves-effect btn-soft-warning btn-assignment-audit-enter w-100"
                                                    data-assignment-id="<?php echo e($assignmentId); ?>">
                                                        <i class="ri-subtract-line label-icon align-middle fs-16"></i> Revogar
                                                    </button>
                                                </div>
                                                <div class="col-6 ps-1">
                                                    <a
                                                    <?php if($surveyorStatus == 'completed'): ?>
                                                        href="<?php echo e(route('formAuditorAssignmentURL', $assignmentId)); ?>"
                                                    <?php else: ?>
                                                        onclick="alert('Necessário aguardar finalização da Vistoria')"
                                                    <?php endif; ?>
                                                    class="btn btn-sm btn-label right waves-effect btn-soft-secondary w-100" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top" title="Abrir formulário">
                                                        <i class="ri-fingerprint-2-line label-icon align-middle fs-16"></i> Auditar
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="form-text mt-2" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="left" title="A data limite para realizar esta tarefa"><?php echo e($assignmentCreatedAt ? 'Prazo: ' . \Carbon\Carbon::parse($assignmentCreatedAt)->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY') : ''); ?></div>
                                        <?php else: ?>
                                            

                                            <div class="form-text text-warning text-opacity-75 mt-2" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="left" title="A data limite para realizar esta tarefa">
                                                <?php echo e($assignmentCreatedAt ? 'Prazo: ' . \Carbon\Carbon::parse($assignmentCreatedAt)->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY') : ''); ?>

                                            </div>
                                        <?php endif; ?>
                                    <?php elseif($auditorStatus == 'completed'): ?>
                                        <span class="text-success">De Acordo</span>: <?php echo e($complianceAuditorYesCount); ?>

                                        <br><br>
                                        <span class="text-danger">Indeferido</span>: <?php echo e($complianceAuditorNoCount); ?>

                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body h-100">
                            <div id="polarTermsAreaChart"></div>
                        </div>
                    </div>
                </div>
            </div>

            

            <h5 class="text-uppercase">Resultado:</h5>
            <?php $__currentLoopData = $stepsWithTopics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stepIndex => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $topicBadgeIndex = 0;

                    $stepId = isset($step['step_id']) ? intval($step['step_id']) : '';
                    $termId = isset($step['term_id']) ? intval($step['term_id']) : '';
                    // use the term_id to get term name. If term_id is less than 9000, find the getDepartmentNameById(term_id)
                    $termName = $termId >= 100000 ? getWarehouseTermNameById($termId) : getTermNameById($termId);
                    //$type =
                    $originalPosition = isset($step['step_order']) ? intval($step['step_order']) : 0;
                    $newPosition = $originalPosition;
                    $topics = $step['topics'];
                ?>

                <?php if( $topics ): ?>
                    <div class="card joblist-card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="job-title text-theme text-uppercase"><?php echo e($termName); ?></h5>
                        </div>
                        <?php if( $topics && is_array($topics)): ?>
                            <?php
                                $bg = 'bg-opacity-75';
                            ?>
                            <?php $__currentLoopData = $topics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topicIndex => $topic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $topicBadgeIndex++;

                                    $topicId = isset($topic['topic_id']) ? intval($topic['topic_id']) : '';
                                    $question = $topic['question'] ?? '';

                                    $originalPosition = 0;
                                    $newPosition = 0;

                                    $stepIdToFind = $stepId;
                                    $topicIdToFind = $topicId;

                                    $filteredItems = array_filter($responsesData, function ($item) use ($stepIdToFind, $topicIdToFind) {
                                        return $item['step_id'] == $stepIdToFind && $item['topic_id'] == $topicIdToFind;
                                    });

                                    // Reset array keys
                                    $filteredItems = array_values($filteredItems);

                                    $responseId = $filteredItems[0]['id'] ?? '';

                                    $surveyAttachmentIds =  $filteredItems[0]['attachments_survey'] ?? '';
                                    $surveyAttachmentIds = $surveyAttachmentIds ? json_decode($surveyAttachmentIds, true) : '';

                                    $auditAttachmentIds =  $filteredItems[0]['attachments_audit'] ?? '';
                                    $auditAttachmentIds = $auditAttachmentIds ? json_decode($auditAttachmentIds, true) : '';

                                    $commentSurvey = $filteredItems[0]['comment_survey'] ?? '';
                                    $complianceSurvey = $filteredItems[0]['compliance_survey'] ?? '';

                                    $commentAudit = $filteredItems[0]['comment_audit'] ?? '';
                                    $complianceAudit = $filteredItems[0]['compliance_audit'] ?? '';

                                    $bgSurveyor = $complianceSurvey == 'yes' ? 'bg-opacity-10 bg-success' : 'bg-opacity-10 bg-danger';
                                    $bgSurveyor = $complianceSurvey ? $bgSurveyor : 'bg-opacity-10 bg-warning';

                                    $bgAuditor = $complianceAudit == 'yes' ? 'bg-opacity-10 bg-success' : 'bg-opacity-10 bg-danger';
                                    $bgAuditor = $complianceAudit ? $bgAuditor : 'bg-opacity-10 bg-warning';

                                    $topicBadgeColor = $complianceAudit == 'no' ? 'danger' : 'success'; // $complianceSurvey == 'no' ||

                                    if($complianceSurvey && $complianceAudit){
                                        $topicLabelColor = $complianceAudit == 'no' ? '<span class="ri-emotion-unhappy-fill text-danger float-end blink fs-3 mt-n2" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top" title="Não Conforme"></span>' : '<span class="ri-emotion-fill text-success float-end fs-3 mt-n2" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top" title="Em conformidade"></span>'; // $complianceSurvey == 'no' ||
                                    }else{
                                        $topicLabelColor = $auditorId ? '<span class="fs-4 ri-alert-fill text-warning float-end" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top" title="Não Comparável"></span>' : '';
                                    }

                                ?>
                                <div class="card-body pb-0">
                                    <?php echo $topicLabelColor; ?>

                                    <h5 class="mb-0">
                                        <span class="badge bg-light-subtle badge-border text-<?php echo e($topicBadgeColor); ?> align-bottom me-1"><?php echo e($topicBadgeIndex); ?></span>
                                        <?php echo e($question ? ucfirst($question) : 'NI'); ?>

                                    </h5>
                                    <div class="row mt-3">
                                        <div class="<?php echo e($auditorId ? 'col-md-6' : 'col-md-12'); ?> pb-3">
                                            <div class="card border-0 h-100">
                                                <div class="card-header border-1 border-bottom-dashed <?php echo e($bgSurveyor); ?>">
                                                    <h6 class="card-title mb-0">
                                                        <?php if($auditorId): ?>
                                                            Checklist:
                                                        <?php endif; ?>
                                                        <?php echo $complianceSurvey && $complianceSurvey == 'yes' ? '<span class="text-theme">Conforme</span>' : ''; ?>

                                                        <?php echo $complianceSurvey && $complianceSurvey == 'no' ? '<span class="text-danger">Não Conforme</span>' : ''; ?>

                                                        <?php echo !$complianceSurvey ? '<span class="text-warning">Não Informado</span>' : ''; ?>

                                                    </h6>
                                                </div>

                                                <div class="card-body <?php echo e($bgSurveyor); ?> pb-0">
                                                    <?php echo $commentSurvey ? '<p>'.nl2br($commentSurvey).'</p>' : ''; ?>


                                                    <?php if( !empty($surveyAttachmentIds) && is_array($surveyAttachmentIds) ): ?>
                                                        <div class="row">
                                                            <?php $__currentLoopData = $surveyAttachmentIds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachmentId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <?php
                                                                    $attachmentUrl = $dateAttachment = '';
                                                                    if (!empty($attachmentId)) {
                                                                        $attachmentUrl = App\Models\Attachments::getAttachmentPathById($attachmentId);

                                                                        $dateAttachment = App\Models\Attachments::getAttachmentDateById($attachmentId);
                                                                    }
                                                                ?>
                                                                <?php if($attachmentUrl): ?>
                                                                    <div class="element-item col-auto">
                                                                        <div class="gallery-box card p-0 m-1">
                                                                            <div class="gallery-container">
                                                                                <a href="<?php echo e($attachmentUrl); ?>" class="image-popup" title="Imagem capturada em <?php echo e($dateAttachment); ?>hs" data-gallery="gallery-<?php echo e($responseId); ?>">
                                                                                    <img class="rounded gallery-img" alt="image" height="70" src="<?php echo e($attachmentUrl); ?>">

                                                                                    <div class="gallery-overlay">
                                                                                        <h5 class="overlay-caption fs-10"><?php echo e($dateAttachment); ?></h5>
                                                                                    </div>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php endif; ?>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="<?php echo e($auditorId ? 'col-md-6' : 'd-none'); ?> pb-3">
                                            <div class="card border-0 h-100">
                                                <div class="card-header border-1 border-bottom-dashed <?php echo e($bgAuditor); ?>">
                                                    <h6 class="card-title mb-0">
                                                        Auditoria:
                                                        <?php echo $complianceAudit && $complianceAudit == 'yes' ? '<span class="text-theme">Aprovada</span>' : ''; ?>

                                                        <?php echo $complianceAudit && $complianceAudit == 'no' ? '<span class="text-danger">Indeferida</span>' : ''; ?>

                                                        <?php echo !$complianceAudit ? '<span class="text-warning">Não Informado</span>' : ''; ?>

                                                    </h6>
                                                </div>
                                                <div class="card-body <?php echo e($bgAuditor); ?> pb-0">
                                                    <?php echo $commentAudit ? '<p>'.nl2br($commentAudit).'</p>' : ''; ?>


                                                    <?php if( !empty($auditAttachmentIds) && is_array($auditAttachmentIds) ): ?>
                                                        <div class="row">
                                                            <?php $__currentLoopData = $auditAttachmentIds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachmentId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <?php
                                                                    $attachmentUrl = $dateAttachment = '';
                                                                    if (!empty($attachmentId)) {
                                                                        $attachmentUrl = App\Models\Attachments::getAttachmentPathById($attachmentId);

                                                                        $dateAttachment = App\Models\Attachments::getAttachmentDateById($attachmentId);
                                                                    }
                                                                ?>
                                                                <?php if($attachmentUrl): ?>
                                                                    <div class="element-item col-auto">
                                                                        <div class="gallery-box card p-0 m-1">
                                                                            <div class="gallery-container">
                                                                                <a href="<?php echo e($attachmentUrl); ?>" class="image-popup" title="Imagem capturada em <?php echo e($dateAttachment); ?>hs" data-gallery="gallery-<?php echo e($responseId); ?>">
                                                                                    <img class="rounded gallery-img" alt="image" height="70" src="<?php echo e($attachmentUrl); ?>">

                                                                                    <div class="gallery-overlay">
                                                                                        <h5 class="overlay-caption fs-10"><?php echo e($dateAttachment); ?></h5>
                                                                                    </div>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php endif; ?>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </div>

    <?php if($surveyorStatus != 'completed'): ?>
        <div class="fixed-bottom mb-0 ms-auto me-auto w-100" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="left" title="Esta barra indica a evolução de uma tarefa com base no percentual">
            <div class="flex-grow-1">
                <div class="progress animated-progress progress-label rounded-0">
                    <div class="progress-bar rounded-0 bg-<?php echo e(getProgressBarClass($percentage)); ?>" role="progressbar" style="width: <?php echo e($percentage); ?>%" aria-valuenow="" aria-valuemin="0" aria-valuemax="100">
                        <div class="label"><?php echo e($percentage > 0 ? $percentage.'%' : ''); ?></div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        var formAuditorAssignmentURL = "<?php echo e(route('formAuditorAssignmentURL')); ?>";
        var changeAssignmentAuditorStatusURL = "<?php echo e(route('changeAssignmentAuditorStatusURL')); ?>";
        var responsesAuditorStoreOrUpdateURL = "<?php echo e(route('responsesAuditorStoreOrUpdateURL')); ?>";
        var enterAssignmentAuditorURL = "<?php echo e(route('enterAssignmentAuditorURL')); ?>";
        
        var revokeAssignmentAuditorURL = "<?php echo e(route('revokeAssignmentAuditorURL')); ?>";
    </script>
    <script src="<?php echo e(URL::asset('build/js/surveys-auditor.js')); ?>" type="module"></script>

    <script type="module">
        import {
            autoReloadPage,
        } from '<?php echo e(URL::asset('build/js/helpers.js')); ?>';

        <?php if($surveyorStatus == 'in_progress'): ?>
            autoReloadPage(60);
        <?php endif; ?>
    </script>

    <?php if($countResponses ): ?>
        <script src="<?php echo e(URL::asset('build/libs/glightbox/js/glightbox.min.js')); ?>"></script>

        <script src="<?php echo e(URL::asset('build/libs/apexcharts/apexcharts.min.js')); ?>"></script>

        <script>
            const rawTermsData = <?php echo json_encode($analyticTermsData, 15, 512) ?>;
            const terms = <?php echo json_encode($terms, 15, 512) ?>;

            document.addEventListener('DOMContentLoaded', function() {
                ///////////////////////////////////////////////////////////////
                // START #barTermsChart
                var seriesData = [];
                var categories = [];

                for (var termId in rawTermsData) {
                    var totalComplianceYes = 0;
                    var totalComplianceNo = 0;

                    for (var date in rawTermsData[termId]) {
                        var termData = rawTermsData[termId][date];
                        totalComplianceYes += termData.filter(item => item.compliance_survey === 'yes').length;
                        totalComplianceNo += termData.filter(item => item.compliance_survey === 'no').length;
                    }

                    seriesData.push({
                        x: terms[termId]['name'],
                        y: totalComplianceYes - totalComplianceNo
                    });

                    categories.push(terms[termId]['name']);
                }

                var optionsTermsChart = {
                    series: [{
                        name: 'Score',
                        data: seriesData
                    }],
                    title: {
                        text: 'Dinâmica de Pontuação na Conformidade entre Termos'
                    },
                    chart: {
                        type: 'bar',
                        height: 402,
                        toolbar: {
                            show: false,
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '55%',
                            colors: {
                                ranges: [{
                                    from: -1000,
                                    to: 0,
                                    color: '#DF5253'
                                }, {
                                    from: 1,
                                    to: 1000,
                                    color: '#1FDC01'
                                }],
                            },
                            dataLabels: {
                                position: 'top',
                            },
                        },
                    },
                    xaxis: {
                        categories: categories
                    },
                    fill: {
                        opacity: 1
                    },
                };

                var barTermsChart = new ApexCharts(document.querySelector("#barTermsChart"), optionsTermsChart);
                barTermsChart.render();
                // END #barTermsChart
                ///////////////////////////////////////////////////////////////

                ///////////////////////////////////////////////////////////////
                // START #mixedTermsChart
                var columnSeriesData = [];
                var lineSeriesData = [];
                var categories = [];

                for (var termId in rawTermsData) {
                    var totalComplianceYes = 0;
                    var totalComplianceNo = 0;

                    for (var date in rawTermsData[termId]) {
                        var termData = rawTermsData[termId][date];
                        totalComplianceYes += termData.filter(item => item.compliance_survey === 'yes').length;
                        totalComplianceNo += termData.filter(item => item.compliance_survey === 'no').length;
                    }

                    columnSeriesData.push(totalComplianceYes);
                    lineSeriesData.push(totalComplianceNo);
                    categories.push(terms[termId]['name']);
                }

                var optionsMixedTermsChart = {
                    series: [{
                        name: 'Conforme',
                        type: 'column',
                        data: columnSeriesData
                    }, {
                        name: 'Não Conforme',
                        type: 'line',
                        data: lineSeriesData
                    }],
                    chart: {
                        height: 402,
                        type: 'line',
                        toolbar: {
                            show: false,
                        }
                    },
                    stroke: {
                        width: [0, 4]
                    },
                    title: {
                        text: 'Insights Comparativos de Conformidade'// Compliance Overview by Term
                    },
                    dataLabels: {
                        enabled: true,
                        enabledOnSeries: [1]
                    },
                    labels: categories,
                    xaxis: {
                        type: 'category'
                    },
                    yaxis: [{
                        title: {
                            text: 'Conforme'
                        }
                    }, {
                        opposite: true,
                        title: {
                            text: 'Não Conforme'
                        }
                    }],
                    colors: ['#1FDC01', '#DF5253']  // Assign custom colors to Compliance Yes and No
                };

                var mixedTermsChart = new ApexCharts(document.querySelector("#mixedTermsChart"), optionsMixedTermsChart);
                mixedTermsChart.render();
                // END #mixedTermsChart
                ///////////////////////////////////////////////////////////////

                ///////////////////////////////////////////////////////////////
                // START #polarTermsAreaChart
                var seriesData = [];
                var labels = [];

                var termMetrics = {};

                // Aggregate data for each term
                for (var termId in rawTermsData) {
                    for (var date in rawTermsData[termId]) {
                        var termData = rawTermsData[termId][date];
                        var totalCompliance = termData.filter(item => item.compliance_survey === 'yes').length;

                        if (!termMetrics[termId]) {
                            termMetrics[termId] = 0;
                        }
                        termMetrics[termId] += totalCompliance;
                    }
                }

                // Prepare data for the chart
                for (var termId in termMetrics) {
                    seriesData.push(termMetrics[termId]);
                    // Assuming 'terms' is an object where keys are term IDs and values contain term details
                    labels.push(terms[termId]['name']);
                }

                var optionsTermsAreaChart = {
                    series: seriesData,
                    chart: {
                        height: 279,
                        type: 'polarArea',
                        toolbar: {
                            show: false,
                        }
                    },
                    title: {
                        text: 'Análise Polar de Conformidade'// Terms Compliance Polar Analysis
                    },
                    labels: labels,
                    stroke: {
                        colors: ['#fff']
                    },
                    fill: {
                        opacity: 0.8
                    },
                    legend: {
                        show: true,
                        position: 'bottom'
                    },
                    yaxis: {
                        show: false // Disable Y-axis labels
                    },
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 200
                            }
                        }
                    }]
                };

                var polarTermsAreaChart = new ApexCharts(document.querySelector("#polarTermsAreaChart"), optionsTermsAreaChart);
                polarTermsAreaChart.render();
                // END #polarTermsAreaChart
                ///////////////////////////////////////////////////////////////

            });
        </script>
    <?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/surveys/assignment/show.blade.php ENDPATH**/ ?>