<?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stepIndex => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $stepData = $step['stepData'] ?? null;
        $stepName = $stepData['step_name'] ?? 'NI';
        $termId = $stepData['term_id'] ?? 0;
        $type = $stepData['type'] ?? 'custom';
        $originalPosition = $stepData['original_position'] ?? $stepIndex;
        $newPosition = $stepData['new_position'] ?? $originalPosition;

        $topics = $step['topics'] ?? null;
        $topics = $topics && is_array($topics) ? array_filter($topics) : $topics;
    ?>
    <div id="<?php echo e($termId); ?>" class="accordion-item block-item mt-3 mb-0 border-dark border-1 rounded rounded-2 p-0">
        <div class="input-group">
            <input type="text" class="form-control text-theme" name="steps[<?php echo e($stepIndex); ?>]['stepData']['step_name']" value="<?php echo e($stepName); ?>" placeholder="Setor/Etapa" maxlength="100" readonly required tabindex="-1">

            <div class="btn btn-ghost-dark btn-icon rounded-pill cursor-n-resize handle-block ri-arrow-up-down-line text-body" title="Reordenar"></div>

            <button type="button" class="btn btn-ghost-dark btn-icon rounded-pill btn-accordion-toggle ri-arrow-up-s-line" tabindex="-1"></button>
        </div>

        <input type="hidden" name="steps[<?php echo e($stepIndex); ?>]['stepData']['term_id']" value="<?php echo e($termId); ?>">
        <input type="hidden" name="steps[<?php echo e($stepIndex); ?>]['stepData']['type']" value="<?php echo e($type); ?>">
        <input type="hidden" name="steps[<?php echo e($stepIndex); ?>]['stepData']['original_position']" value="<?php echo e($originalPosition); ?>">
        <input type="hidden" name="steps[<?php echo e($stepIndex); ?>]['stepData']['new_position']" value="<?php echo e($newPosition); ?>">

        <div class="accordion-collapse collapse show">
            <div class="nested-sortable-topic mt-0 p-1"><?php if( isset($topics) && is_array($topics) && count($topics) > 0 ): ?>
                <?php $__currentLoopData = $topics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topicIndex => $topic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $question = $topic['question'] ?? '';
                        $originalPosition = $topic['original_position'] ?? $topicIndex;
                        $newPosition = $topic['new_position'] ?? $originalPosition;
                    ?>
                    <div id="<?php echo e($termId . $topicIndex); ?>" class="step-topic mt-1 mb-1">
                        <div class="row">
                            <div class="col-auto">
                                <button type="button" class="btn btn-ghost-danger btn-icon rounded-pill btn-remove-topic ri-delete-bin-3-line" data-target="<?php echo e($termId . $topicIndex); ?>" title="Remover Tópico" tabindex="-1"></button>
                            </div>
                            <div class="col">
                                <input type="text" class="form-control" title="Exemplo: Organização do setor?... Abastecimento de produtos/insumos está em dia? " placeholder="Tópico..." name="steps[<?php echo e($stepIndex); ?>]['topics']['question']" value="<?php echo e($question); ?>" maxlength="150" required>
                            </div>
                            <div class="col-auto">
                                <div class="btn btn-ghost-dark btn-icon rounded-pill cursor-n-resize handle-topic ri-arrow-up-down-line" title="Reordenar Tópico"></div>
                            </div>
                            <input type="hidden" name="steps[<?php echo e($stepIndex); ?>]['topics']['original_position']" tabindex="-1" value="<?php echo e($originalPosition); ?>">
                            <input type="hidden" name="steps[<?php echo e($stepIndex); ?>]['topics']['new_position']" tabindex="-1" value="<?php echo e($newPosition); ?>">
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?></div>

            <div class="clearfix">
                <button type="button" class="btn btn-ghost-dark btn-icon btn-add-topic rounded-pill float-end cursor-copy text-theme ri-menu-add-line" data-block-step-id="<?php echo e($termId); ?>" data-block-index="<?php echo e($stepIndex); ?>" title="Adicionar Tópico"></button>

                <?php if( $type == 'custom' ): ?>
                    <button type="button" class="btn btn-ghost-danger btn-icon rounded-pill btn-remove-block float-end ri-delete-bin-7-fill" data-target="<?php echo e($termId); ?>" title="Remover Bloco" tabindex="-1"></button>
                <?php endif; ?>
            </div>

        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/surveys/templates/form.blade.php ENDPATH**/ ?>