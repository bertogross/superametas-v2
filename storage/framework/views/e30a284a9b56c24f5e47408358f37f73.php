<?php if( $data ): ?>
    <?php
        //appPrintR($responsesData);
        $radioIndex = $badgeIndex = $countFinished = $countTopics = 0;
    ?>
    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stepIndex => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            if( isset($purpose) && $purpose == 'validForm' ){
                $stepId = intval($step['step_id']);
                $termId = intval($step['term_id']);
                // use the term_id to get term name. If term_id is less than 9000, find the getDepartmentNameById(term_id)
                $stepName = $termId < 9000 ? getDepartmentNameById($termId) : getTermNameById($termId);
                //$type =
                $originalPosition = intval($step['step_order']);
                $newPosition = $originalPosition;
                $topics = $step['topics'];
            }else{
                $stepId = '';
                $stepData = $step['stepData'] ?? null;
                $stepName = $stepData['step_name'] ?? '';
                $termId = $stepData['term_id'] ?? '';
                //$type = $stepData['type'] ?? 'custom';
                $originalPosition = $stepData['original_position'] ?? $stepIndex;
                $newPosition = $stepData['new_position'] ?? $originalPosition;
                $topics = $step['topics'] ?? null;
            }
        ?>

        <?php if( $topics ): ?>
            <div class="card joblist-card">
                <div class="card-body">
                    <h5 class="job-title text-theme"><?php echo e($stepName); ?></h5>
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

                            if( isset($purpose) && $purpose == 'validForm' ){
                                $topicId = intval($topic['topic_id']);
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

                                $attachmentIds =  $filteredItems[0]['attachments_survey'] ?? '';
                                $attachmentIds = $attachmentIds ? json_decode($attachmentIds, true) : '';

                                if($responseId){
                                    $countFinished++;
                                }
                                $commentSurvey = $filteredItems[0]['comment_survey'] ?? '';
                                $complianceSurvey = $filteredItems[0]['compliance_survey'] ?? '';
                            }else{
                                $topicId = '';
                                $question = $topic['question'] ?? '';
                                $originalPosition = $topic['original_position'] ?? $topicIndex;
                                $newPosition = $topic['new_position'] ?? $originalPosition;

                                $responseId = '';
                                $commentSurvey = '';
                                $complianceSurvey = '';
                            }
                        ?>
                        <div class="card-footer border-top-dashed <?php echo e($bg); ?>">
                            <form class="responses-data-container" autocomplete="off">
                                <input type="hidden" name="response_id" value="<?php echo e($responseId ?? ''); ?>">

                                <div class="row">
                                    <div class="col">
                                        <h5 class="mb-0">
                                            <span class="badge bg-light-subtle text-body badge-border text-theme align-bottom me-1"><?php echo e($topicBadgeIndex); ?></span>
                                            <?php echo e($question); ?>

                                        </h5>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fs-5 ri-time-line text-warning-emphasis <?php echo e(!$responseId ? '' : 'd-none'); ?>"
                                        data-bs-toggle="tooltip" data-bs-trigger="hover"
                                        data-bs-placement="top" title="Status: Pendente"></i>

                                        <i class="fs-5 ri-check-double-fill text-theme <?php echo e($responseId ? '' : 'd-none'); ?>"
                                        data-bs-toggle="tooltip"
                                        data-bs-trigger="hover"
                                        data-bs-placement="top" title="Status: Concluído"></i>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col">
                                        <div class="input-group">
                                            <?php if( $surveyorStatus != 'auditing' && $surveyorStatus != 'losted' ): ?>
                                                <label for="input-attachment-<?php echo e($radioIndex); ?>" class="btn btn-outline-light waves-effect waves-light ps-1 pe-1 mb-0 d-flex align-content-center flex-wrap" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Anexar fotografia" data-step-id="<?php echo e($stepId); ?>" data-topic-id="<?php echo e($topicId); ?>">
                                                    <i class="ri-image-add-fill text-body fs-5 m-2"></i>
                                                </label>
                                                <input type="file" id="input-attachment-<?php echo e($radioIndex); ?>" class="input-upload-photo d-none" accept="image/jpeg" <?php echo e(isset($purpose) && $purpose == 'validForm' ? '' : 'disabled'); ?>>
                                            <?php endif; ?>

                                            <textarea tabindex="-1" class="form-control border-light" maxlength="1000" rows="3" name="comment_survey" placeholder="Observações..." <?php echo e($surveyorStatus == 'auditing' || $surveyorStatus == 'losted' ? 'disabled readonly' : ''); ?> style="max-height: 70px;"><?php echo e($commentSurvey ?? ''); ?></textarea>
                                        </div>

                                        <?php if( $surveyorStatus != 'auditing' && $surveyorStatus != 'losted' ): ?>
                                            <button tabindex="-1"
                                                type="button"
                                                <?php if( isset($purpose) && $purpose == 'validForm' ): ?>
                                                    data-assignment-id="<?php echo e($assignmentId); ?>"
                                                    data-step-id="<?php echo e($stepId); ?>"
                                                    data-topic-id="<?php echo e($topicId); ?>"
                                                <?php endif; ?>
                                                data-bs-toggle="tooltip"
                                                data-bs-trigger="hover"
                                                data-bs-placement="left"
                                                title="<?php echo e($responseId ? 'Atualizar' : 'Salvar'); ?>"
                                                class="btn btn-outline-light waves-effect waves-light ps-1 pe-1 <?php echo e(isset($purpose) && $purpose == 'validForm' ? 'btn-response-update' : ''); ?> d-none">
                                                    <i class="<?php echo e($responseId ? 'ri-refresh-line' : 'ri-save-3-line'); ?> text-theme fs-3 m-2"></i>
                                            </button>
                                        <?php endif; ?>

                                        <div class="gallery-wrapper mt-2 row">
                                            <?php if( !empty($attachmentIds) && is_array($attachmentIds) ): ?>
                                                <?php $__currentLoopData = $attachmentIds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachmentId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php
                                                        $attachmentUrl = $dateAttachment = '';
                                                        if (!empty($attachmentId)) {
                                                            $attachmentUrl = App\Models\Attachments::getAttachmentPathById($attachmentId);

                                                            $dateAttachment = App\Models\Attachments::getAttachmentDateAttachmentById($attachmentId);
                                                        }
                                                    ?>
                                                    <?php if($attachmentUrl): ?>
                                                        <div id="element-attachment-<?php echo e($attachmentId); ?>" class="element-item col-auto">
                                                            <div class="gallery-box card p-0">
                                                                <div class="gallery-container">
                                                                    <a href="<?php echo e($attachmentUrl); ?>" class="image-popup" title="Fotografia capturada em <?php echo e($dateAttachment); ?>hs">
                                                                        <img class="rounded gallery-img" alt="image" height="70" src="<?php echo e($attachmentUrl); ?>">

                                                                        <div class="gallery-overlay">
                                                                            <h5 class="overlay-caption fs-10"><?php echo e($dateAttachment); ?></h5>
                                                                        </div>
                                                                    </a>
                                                                </div>
                                                            </div>

                                                            <?php if( $surveyorStatus != 'auditing' && $surveyorStatus != 'losted' ): ?>
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
                                    <div class="col-auto">
                                        <div class="form-check form-switch form-switch-sm form-switch-theme mb-4" title="Em conformidade">
                                            <input tabindex="-1" class="form-check-input" type="radio" name="compliance_survey" role="switch" id="YesSwitchCheck<?php echo e($topicIndex.$radioIndex); ?>" <?php echo e($surveyorStatus == 'auditing' || $surveyorStatus == 'losted' ? 'disabled' : ''); ?> value="yes" <?php echo e($complianceSurvey && $complianceSurvey == 'yes' ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="YesSwitchCheck<?php echo e($topicIndex.$radioIndex); ?>">Conforme</label>
                                        </div>
                                        <div class="form-check form-switch form-switch-sm form-switch-danger" title="Não conforme">
                                            <input tabindex="-1" class="form-check-input" type="radio" name="compliance_survey" role="switch" id="NoSwitchCheck<?php echo e($topicIndex.$radioIndex); ?>" <?php echo e($surveyorStatus == 'auditing' || $surveyorStatus == 'losted' ? 'disabled' : ''); ?> value="no" <?php echo e($complianceSurvey && $complianceSurvey == 'no' ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="NoSwitchCheck<?php echo e($topicIndex.$radioIndex); ?>">Não Conforme</label>
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

    <?php if( isset($purpose) && $purpose == 'validForm' && $surveyorStatus != 'auditing' && $surveyorStatus != 'losted' ): ?>
        <button tabindex="-1"
            type="button"
            class="btn btn-lg btn-theme waves-effect w-100 <?php echo e($countFinished < $countTopics ? 'd-none' : ''); ?>"
            id="btn-response-finalize"
            data-assignment-id="<?php echo e($assignmentId); ?>"
            title="Finalizar e Enviar para Auditoria">
            <i class="ri-send-plane-fill align-bottom m-2"></i> Finalizar e Enviar para Auditoria
        </button>
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views\surveys\layouts\form-surveyor-step-cards.blade.php ENDPATH**/ ?>