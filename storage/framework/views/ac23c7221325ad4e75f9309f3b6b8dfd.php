<?php if( $data ): ?>
    <?php
        //appPrintR($responsesData);
        $radioIndex = $badgeIndex = $countFinished = $countTopics = 0;
    ?>
    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stepIndex => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $stepId = isset($step['step_id']) ? intval($step['step_id']) : '';
            $termId = isset($step['term_id']) ? intval($step['term_id']) : '';
            // use the term_id to get term name. If term_id is less than 9000, find the getDepartmentNameById(term_id)
            $stepName = $termId < 9000 ? getDepartmentNameById($termId) : getTermNameById($termId);
            //$type =
            $originalPosition = isset($step['step_order']) ? intval($step['step_order']) : 0;
            $newPosition = $originalPosition;
            $topics = $step['topics'];
        ?>

        <?php if( $topics ): ?>
            <div class="card joblist-card">
                <div class="card-body">
                    <h5 class="job-title text-theme text-uppercase"><?php echo e($stepName); ?></h5>
                </div>
                <?php if( $topics && is_array($topics)): ?>
                    <?php
                        $bg = 'bg-opacity-75';

                        $topicBadgeIndex = 0;
                    ?>
                    <?php $__currentLoopData = $topics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topicIndex => $topic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $bg = $bg == 'bg-opacity-75' ? 'bg-opacity-50' : 'bg-opacity-75';

                            $radioIndex++;

                            $countTopics++;

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

                            if($complianceAudit){
                                $countFinished++;
                            }
                        ?>
                        <div class="card-footer border-top-dashed pb-0 <?php echo e($bg); ?>">
                            <form class="responses-data-container" autocomplete="off">
                                <input type="hidden" name="response_id" value="<?php echo e($responseId ?? ''); ?>">

                                <div class="row">
                                    <div class="col">
                                        <h5 class="mb-0">
                                            <span class="badge bg-light-subtle text-body badge-border text-theme align-bottom me-1"><?php echo e($topicBadgeIndex); ?></span>
                                            <?php echo e($question ? ucfirst($question) : 'NI'); ?>

                                        </h5>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fs-5 ri-time-line text-warning-emphasis <?php echo e(!$complianceAudit ? '' : 'd-none'); ?>" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Status: Pendente"></i>

                                        <i class="fs-5 ri-check-double-fill text-theme <?php echo e($complianceAudit ? '' : 'd-none'); ?>" data-bs-placement="top" title="Status: Concluído"></i>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-4 pb-3">
                                        <div class="card border border-light rounded rounded-1 h-100">
                                            <div class="card-header bg-dark">
                                                <h6 class="card-title mb-0">
                                                    Vistoria: <?php echo $complianceSurvey && $complianceSurvey == 'yes' ? '<span class="text-theme">Conforme</span>' : '<span class="text-danger">Não Conforme</span>'; ?>

                                                </h6>
                                            </div>
                                            <div class="card-body bg-dark">
                                                <?php echo $commentSurvey ? '<p>'.nl2br($commentSurvey).'</p>' : ''; ?>


                                                <div class="mt-2 row">
                                                    <?php if( !empty($surveyAttachmentIds) && is_array($surveyAttachmentIds) ): ?>
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
                                                                    <div class="gallery-box card p-0">
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
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8 pb-3">
                                        <div class="card border border-light rounded rounded-1 h-100">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">
                                                    Auditoria
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-sm-12 col-md">
                                                        <div class="input-group">
                                                            <?php if( $auditorStatus != 'completed' && $auditorStatus != 'losted' ): ?>
                                                                <label for="input-attachment-<?php echo e($radioIndex); ?>" class="btn btn-outline-light waves-effect waves-light ps-1 pe-1 mb-0 d-flex align-content-center flex-wrap" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Anexar fotografia" data-step-id="<?php echo e($stepId); ?>" data-topic-id="<?php echo e($topicId); ?>">
                                                                    <i class="ri-image-add-fill text-body fs-5 m-2"></i>
                                                                </label>
                                                                <input type="file" id="input-attachment-<?php echo e($radioIndex); ?>" class="input-upload-photo d-none" accept="image/jpeg">
                                                            <?php endif; ?>

                                                            <textarea tabindex="-1" class="form-control border-light" maxlength="1000" rows="3" name="comment_audit" placeholder="Observações..." <?php echo e($auditorStatus == 'auditing' || $auditorStatus == 'losted' ? 'disabled readonly' : ''); ?> style="height: 70px;"><?php echo e($commentAudit ?? ''); ?></textarea>
                                                        </div>

                                                        <?php if( $auditorStatus != 'completed' && $auditorStatus != 'losted' ): ?>
                                                            <button tabindex="-1"
                                                                type="button"
                                                                data-assignment-id="<?php echo e($assignmentId); ?>"
                                                                data-step-id="<?php echo e($stepId); ?>"
                                                                data-topic-id="<?php echo e($topicId); ?>"
                                                                data-bs-toggle="tooltip"
                                                                data-bs-trigger="hover"
                                                                data-bs-placement="left"
                                                                title="<?php echo e($responseId ? 'Atualizar' : 'Salvar'); ?>"
                                                                class="btn btn-outline-light waves-effect waves-light ps-1 pe-1 btn-response-update d-none">
                                                                    <i class="<?php echo e($responseId ? 'ri-refresh-line' : 'ri-save-3-line'); ?> text-theme fs-3 m-2"></i>
                                                            </button>
                                                        <?php endif; ?>

                                                        <div class="gallery-wrapper mt-2 row">
                                                            <?php if( !empty($auditAttachmentIds) && is_array($auditAttachmentIds) ): ?>
                                                                <?php $__currentLoopData = $auditAttachmentIds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachmentId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <?php
                                                                        $attachmentUrl = $dateAttachment = '';
                                                                        if (!empty($attachmentId)) {
                                                                            $attachmentUrl = App\Models\Attachments::getAttachmentPathById($attachmentId);

                                                                            $dateAttachment = App\Models\Attachments::getAttachmentDateById($attachmentId);
                                                                        }
                                                                    ?>
                                                                    <?php if($attachmentUrl): ?>
                                                                        <div id="element-attachment-<?php echo e($attachmentId); ?>" class="element-item col-auto">
                                                                            <div class="gallery-box card p-0">
                                                                                <div class="gallery-container">
                                                                                    <a href="<?php echo e($attachmentUrl); ?>" class="image-popup" title="Imagem capturada em <?php echo e($dateAttachment); ?>hs" data-gallery="gallery-<?php echo e($responseId); ?>">
                                                                                        <img class="rounded gallery-img" alt="image" height="70" src="<?php echo e($attachmentUrl); ?>">

                                                                                        <div class="gallery-overlay">
                                                                                            <h5 class="overlay-caption fs-10"><?php echo e($dateAttachment); ?></h5>
                                                                                        </div>
                                                                                    </a>
                                                                                </div>
                                                                            </div>

                                                                            <?php if( $auditorStatus != 'completed' && $auditorStatus != 'losted' ): ?>
                                                                                <div class="position-absolute translate-middle mt-n3">
                                                                                    <div class="avatar-xs">
                                                                                        <button type="button" class="avatar-title bg-light border-0 rounded-circle text-danger cursor-pointer btn-delete-photo" data-attachment-id="<?php echo e($attachmentId); ?>" title="Deletar Arquivo">
                                                                                            <i class="ri-delete-bin-2-line"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            <?php endif; ?>
                                                                            <input type="hidden" name="attachment_id[]" value="<?php echo e($attachmentId); ?>">
                                                                        </div>
                                                                    <?php endif; ?>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12 col-md-auto">
                                                        <div class="row">
                                                            <div class="col col-md-12">
                                                                <div class="form-check form-switch form-switch-sm form-switch-theme mt-2" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Vistoria efetuada de forma correta">
                                                                    <input tabindex="-1" class="form-check-input" type="radio" name="compliance_audit" role="switch" id="YesSwitchCheck<?php echo e($topicIndex.$radioIndex); ?>" <?php echo e($auditorStatus == 'completed' || $auditorStatus == 'losted' ? 'disabled' : ''); ?> value="yes" <?php echo e($complianceAudit && $complianceAudit == 'yes' ? 'checked' : ''); ?>>
                                                                    <label class="form-check-label" for="YesSwitchCheck<?php echo e($topicIndex.$radioIndex); ?>">De Acordo</label>
                                                                </div>
                                                            </div>
                                                            <div class="col col-md-12">
                                                                <div class="form-check form-switch form-switch-sm form-switch-danger mt-2" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Vistoria efetuada de forma incorreta">
                                                                    <input tabindex="-1" class="form-check-input" type="radio" name="compliance_audit" role="switch" id="NoSwitchCheck<?php echo e($topicIndex.$radioIndex); ?>" <?php echo e($auditorStatus == 'completed' || $auditorStatus == 'losted' ? 'disabled' : ''); ?> value="no" <?php echo e($complianceAudit && $complianceAudit == 'no' ? 'checked' : ''); ?>>
                                                                    <label class="form-check-label" for="NoSwitchCheck<?php echo e($topicIndex.$radioIndex); ?>">Não Concordo</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php if( $auditorStatus != 'completed' && $auditorStatus != 'losted' ): ?>
        <button tabindex="-1"
            type="button"
            class="btn btn-lg btn-theme waves-effect w-100 <?php echo e($countFinished < $countTopics ? 'd-none' : ''); ?>"
            id="btn-response-finalize"
            data-assignment-id="<?php echo e($assignmentId); ?>"
            title="Finalizar Auditoria">
            <i class="ri-save-3-line align-bottom m-2"></i> Finalizar Auditoria
        </button>
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/surveys/layouts/form-auditor-step-cards.blade.php ENDPATH**/ ?>