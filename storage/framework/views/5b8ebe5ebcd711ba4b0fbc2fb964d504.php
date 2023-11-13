<?php if( $data ): ?>
    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stepIndex => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $stepData = $step['stepData'] ?? null;
            $stepName = $stepData['step_name'] ?? '';
            $originalPosition = $stepData['original_position'] ?? $stepIndex;
            $newPosition = $stepData['new_position'] ?? $stepIndex;

            $topics = $step['topics'] ?? null;
        ?>

        <?php if($stepData): ?>
            <div class="card joblist-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <h5 class="job-title text-theme"><?php echo e($stepName); ?></h5>
                        </div>
                        <div>
                        </div>
                    </div>
                </div>
                <?php if( $topics && is_array($topics)): ?>
                    <?php
                        $bg = 'bg-opacity-75';
                        $radioIndex = 0;// To prevent radio id duplication
                    ?>
                    <?php $__currentLoopData = $topics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topicIndex => $topic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $bg = $bg == 'bg-opacity-75' ? 'bg-opacity-50' : 'bg-opacity-75';

                            $radioIndex++;

                            $question = $topic['question'] ?? '';
                            $originalPosition = $topic['original_position'] ?? $topicIndex;
                            $newPosition = $topic['new_position'] ?? $topicIndex;

                            $stepTopicIndex = intval($stepIndex.$topicIndex);
                        ?>
                        <div class="card-footer border-top-dashed bg-dark <?php echo e($bg); ?>">
                            <div class="d-flex">
                                <div class="flex-shrink-0 text-uppercase pe-2">
                                    <span class="badge bg-light-subtle text-body badge-border text-theme"><?php echo e($stepIndex); ?></span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="mb-0">
                                                <?php echo e($question); ?>

                                            </h5>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fs-5 ri-time-line text-warning-emphasis" title="Pendente"></i>
                                            <i class="fs-5 ri-check-double-fill text-success-emphasis d-none" title="Concluído"></i>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-auto">
                                            <div class="form-check form-switch form-switch-lg form-switch-theme mb-3">
                                                <input tabindex="-1" class="form-check-input" type="radio" name="compliance[<?php echo e($stepIndex); ?>]" role="switch" id="SwitchCheck<?php echo e($radioIndex); ?>">
                                                <label class="form-check-label" for="SwitchCheck<?php echo e($radioIndex); ?>">Conforme</label>
                                            </div>
                                            <div class="form-check form-switch form-switch-lg form-switch-danger">
                                                <input tabindex="-1" class="form-check-input" type="radio" name="compliance[<?php echo e($stepIndex); ?>]" role="switch" id="SwitchCheck2<?php echo e($radioIndex); ?>">
                                                <label class="form-check-label" for="SwitchCheck2<?php echo e($radioIndex); ?>">Não Conforme</label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group">
                                                <button tabindex="-1" type="button" class="btn btn-outline-dark waves-effect waves-light ps-1 pe-1 dropdown" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Bater foto"><i class="ri-image-add-fill fs-5 m-2"></i></button>

                                                <textarea tabindex="-1" class="form-control maxlength" maxlength="1000" rows="3" name="observations[<?php echo e($stepIndex); ?>]" placeholder="Observações..."></textarea>

                                                <button tabindex="-1" type="button" class="btn btn-outline-dark waves-effect waves-light"><i class="ri-save-3-line fs-3 m-2 text-theme" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" data-step="<?php echo e($stepIndex); ?>" data-topic="<?php echo e($stepIndex); ?>" title="Salvar"></i></button>
                                            </div>
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
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views\surveys\components\steps-card.blade.php ENDPATH**/ ?>