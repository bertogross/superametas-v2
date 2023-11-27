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
                                    <div class="col-auto">
                                        <div class="form-check form-switch form-switch-sm form-switch-theme mb-4" title="Em conformidade">
                                            <input tabindex="-1" class="form-check-input" type="radio" name="compliance_survey" role="switch" id="YesSwitchCheck<?php echo e($topicIndex.$radioIndex); ?>" <?php echo e($surveyorStatus == 'auditing' ? 'disabled' : ''); ?> value="yes" <?php echo e($complianceSurvey && $complianceSurvey == 'yes' ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="YesSwitchCheck<?php echo e($topicIndex.$radioIndex); ?>">Conforme</label>
                                        </div>
                                        <div class="form-check form-switch form-switch-sm form-switch-danger" title="Não conforme">
                                            <input tabindex="-1" class="form-check-input" type="radio" name="compliance_survey" role="switch" id="NoSwitchCheck<?php echo e($topicIndex.$radioIndex); ?>" <?php echo e($surveyorStatus == 'auditing' ? 'disabled' : ''); ?> value="no" <?php echo e($complianceSurvey && $complianceSurvey == 'no' ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="NoSwitchCheck<?php echo e($topicIndex.$radioIndex); ?>">Não Conforme</label>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="input-group">
                                            <?php if($surveyorStatus != 'auditing'): ?>
                                                <button tabindex="-1" type="button" class="btn btn-outline-light waves-effect waves-light ps-1 pe-1 <?php echo e(isset($purpose) && $purpose == 'validForm' ? 'btn-response-upload' : ''); ?>" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Armazenar imagem" data-step-id="<?php echo e($stepId); ?>" data-topic-id="<?php echo e($topicId); ?>"><i class="ri-image-add-fill text-body fs-5 m-2"></i></button>
                                            <?php endif; ?>

                                            <input type="hidden" name="attachment_id_survey">

                                            <textarea tabindex="-1" class="form-control border-light" maxlength="1000" rows="3" name="comment_survey" placeholder="Observações..." <?php echo e($surveyorStatus == 'auditing' ? 'disabled readonly' : ''); ?>><?php echo e($commentSurvey ?? ''); ?></textarea>

                                            <?php if($surveyorStatus != 'auditing'): ?>
                                                <button tabindex="-1"
                                                    type="button"
                                                    <?php if( isset($purpose) && $purpose == 'validForm' ): ?>
                                                        data-assignment-id="<?php echo e($surveyorAssignmentId); ?>"
                                                        data-step-id="<?php echo e($stepId); ?>"
                                                        data-topic-id="<?php echo e($topicId); ?>"
                                                    <?php endif; ?>
                                                    data-bs-toggle="tooltip"
                                                    data-bs-trigger="hover"
                                                    data-bs-placement="top"
                                                    title="<?php echo e($responseId ? 'Atualizar' : 'Salvar'); ?>"
                                                    class="btn btn-outline-light waves-effect waves-light ps-1 pe-1 <?php echo e(isset($purpose) && $purpose == 'validForm' ? 'btn-response-survey-update' : ''); ?>">
                                                        <i class="<?php echo e($responseId ? 'ri-refresh-line' : 'ri-save-3-line'); ?> text-theme fs-3 m-2"></i>
                                                </button>
                                            <?php endif; ?>
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

    <?php if( isset($purpose) && $purpose == 'validForm' && $surveyorStatus != 'auditing' ): ?>
        <button tabindex="-1" type="button"
        class="btn btn-theme waves-effect w-100 <?php echo e($countFinished < $countTopics ? 'd-none' : ''); ?>"
        id="btn-response-surveyor-assignment-finalize" data-assignment-id="<?php echo e($surveyorAssignmentId); ?>"
        title="Finalizar e Enviar para Auditoria">
            <i class="ri-send-plane-fill align-bottom m-2"></i> Finalizar e Enviar para Auditoria
        </button>
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/surveys/layouts/form-step-cards.blade.php ENDPATH**/ ?>