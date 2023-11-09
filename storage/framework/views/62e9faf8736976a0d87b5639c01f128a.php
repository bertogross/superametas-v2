<div class="nested-receiver-block mt-0 p-1"><?php if(isset($topicsData) && is_array($topicsData)): ?>
    <?php $__currentLoopData = $topicsData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topicIndex => $topic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $topicId = $topic['topic_id'] ?? '';
            $originalPosition = $topic['original_position'] ?? 0;
            $originalTopicIndex = $originalPosition ? intval($originalPosition) : 0;
            $newPosition = $topic['new_position'] ?? 0;
        ?>
        <div id="<?php echo e($originalIndex . $originalTopicIndex); ?>" class="step-item mt-1 mb-1">
            <div class="row">
                <div class="col-auto">
                    <span class="btn btn-ghost-danger btn-icon rounded-pill btn-remove-topic" data-target="<?php echo e($originalIndex . $originalTopicIndex); ?>" title="Remover Tópico"><i class="ri-delete-bin-3-line"></i></span>
                </div>
                <div class="col">
                    <select select-one data-choices-removeItem class="form-control surveys-term-choice w-100" title="Exemplo: Organização do setor?... Abastecimento de produtos/insumos está em dia?" data-placeholder="Tópico..." name="[<?php echo e($originalIndex); ?>]['topicData'][<?php echo e($originalTopicIndex); ?>]['topic_id']" required>
                        <option value="<?php echo e($topicId); ?>" selected><?php echo e(getTermNameById($topicId)); ?></option>
                    </select>
                </div>
                <div class="col-auto">
                    <span class="btn btn-ghost-dark btn-icon rounded-pill cursor-n-resize handle-receiver-block" title="Reordenar"><i class="ri-arrow-up-down-line"></i></span>
                </div>
            </div>
            <input type="hidden" name="[<?php echo e($originalIndex); ?>]['topicData'][<?php echo e($originalTopicIndex); ?>]['original_position']" value="<?php echo e($originalPosition); ?>" tabindex="-1">
            <input type="hidden" name="[<?php echo e($originalIndex); ?>]['topicData'][<?php echo e($originalTopicIndex); ?>]['new_position']" value="<?php echo e($newPosition); ?>" tabindex="-1">
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?></div>

<div class="clearfix">
    <span class="btn btn-ghost-dark btn-icon btn-add-topic rounded-pill float-end cursor-copy text-theme" data-block-index="<?php echo e($originalIndex); ?>" title="Adicionar Tópico"><i class="ri-menu-add-line"></i></span>

    <?php if( $type == 'custom' ): ?>
        <span class="btn btn-ghost-danger btn-icon rounded-pill btn-remove-block float-start" data-target="<?php echo e($originalIndex); ?>" title="Remover Bloco"><i class="ri-delete-bin-7-fill"></i></span>
    <?php endif; ?>
</div>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/surveys/includes/topics-input.blade.php ENDPATH**/ ?>