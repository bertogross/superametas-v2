<?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stepIndex => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $stepData = $step['stepData'] ?? null;
        $stepName = $stepData['step_name'] ?? '';
        $originalPosition = $stepData['original_position'] ?? $stepIndex;
        $newPosition = $stepData['new_position'] ?? $stepIndex;

        $topics = $step['topics'] ?? null;
        $topics = !empty($topics) && is_array($topics) ? array_filter($topics) : $topics;
    ?>
    <div id="<?php echo e($stepIndex); ?>" class="accordion-item block-item mt-3 mb-0 border-dark border-1 rounded rounded-2 p-0">
        <div class="input-group">
            <?php if( $type == 'custom' ): ?>
                <input type="text" class="form-control text-theme" name="steps[<?php echo e($stepIndex); ?>]['stepData']['step_name']" value="<?php echo e($stepName); ?>" placeholder="Informe o Título/Setor/Etapa" autocomplete="off" maxlength="100" required>
            <?php else: ?>
                <input type="text" class="form-control disabled text-theme" autocomplete="off" maxlength="100" value="<?php echo e($stepName); ?>"
                data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Para Departamentos, este campo não é editável"
                readonly disabled>
                <input type="hidden" name="steps[<?php echo e($stepIndex); ?>]['stepData']['step_name']" value="<?php echo e($stepName); ?>">
            <?php endif; ?>

            <span class="tn btn-ghost-dark btn-icon rounded-pill cursor-n-resize handle-receiver" title="Reordenar"><i class="ri-arrow-up-down-line text-body"></i></span>

            <span class="tn btn-ghost-dark btn-icon rounded-pill btn-accordion-toggle"><i class="ri-arrow-up-s-line"></i></span>
        </div>

        <input type="hidden" name="steps[<?php echo e($stepIndex); ?>]['stepData']['type']" value="<?php echo e($type); ?>" tabindex="-1">
        <input type="hidden" name="steps[<?php echo e($stepIndex); ?>]['stepData']['original_position']" value="<?php echo e($originalPosition); ?>" tabindex="-1">
        <input type="hidden" name="steps[<?php echo e($stepIndex); ?>]['stepData']['new_position']" value="<?php echo e($newPosition); ?>" tabindex="-1">

        <div class="accordion-collapse collapse show">
            <div class="nested-receiver-block mt-0 p-1"><?php if( isset($topics) && is_array($topics) && count($topics) > 0 ): ?>
                <?php $__currentLoopData = $topics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topicIndex => $topic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $question = $topic['question'] ?? '';
                        $originalPosition = $topic['original_position'] ?? 0;
                        $originalTopicIndex = $originalPosition ? intval($originalPosition) : 0;
                        $newPosition = $topic['new_position'] ?? 0;
                    ?>
                    <div id="<?php echo e($stepIndex . $originalTopicIndex); ?>" class="step-item mt-1 mb-1">
                        <div class="row">
                            <div class="col-auto">
                                <span class="btn btn-ghost-danger btn-icon rounded-pill btn-remove-topic" data-target="<?php echo e($stepIndex . $originalTopicIndex); ?>" title="Remover Tópico"><i class="ri-delete-bin-3-line"></i></span>
                            </div>
                            <div class="col">
                                <input type="text" class="form-control" title="Exemplo: Organização do setor?... Abastecimento de produtos/insumos está em dia? " placeholder="Tópico..." name="steps[<?php echo e($stepIndex); ?>]['topics']['question']" value="<?php echo e($question); ?>" maxlength="150" required>
                            </div>
                            <div class="col-auto">
                                <span class="btn btn-ghost-dark btn-icon rounded-pill cursor-n-resize handle-receiver-block" title="Reordenar"><i class="ri-arrow-up-down-line"></i></span>
                            </div>
                        </div>
                        <input type="hidden" name="steps[<?php echo e($stepIndex); ?>]['topics']['original_position']" value="<?php echo e($originalPosition); ?>" tabindex="-1">
                        <input type="hidden" name="steps[<?php echo e($stepIndex); ?>]['topics']['new_position']" value="<?php echo e($newPosition); ?>" tabindex="-1">
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?></div>

            <div class="clearfix">
                <span class="btn btn-ghost-dark btn-icon btn-add-topic rounded-pill float-end cursor-copy text-theme" data-block-index="<?php echo e($stepIndex); ?>" title="Adicionar Tópico"><i class="ri-menu-add-line"></i></span>

                <?php if( $type == 'custom' ): ?>
                    <span class="btn btn-ghost-danger btn-icon rounded-pill btn-remove-block float-start" data-target="<?php echo e($stepIndex); ?>" title="Remover Bloco"><i class="ri-delete-bin-7-fill"></i></span>
                <?php endif; ?>
            </div>

        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views\surveys\components\template-form.blade.php ENDPATH**/ ?>