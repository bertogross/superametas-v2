<?php if(isset($topicData) && is_array($topicData)): ?>
    <?php $__currentLoopData = $topicData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topicIndex => $topic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $topicName = $topic['topic_name'] ?? '';
            $originalPosition = $topic['original_position'] ?? '';
            $originalTopicIndex = intval($originalPosition);
            $newPosition = $topic['new_position'] ?? '';
        ?>
        <div class="input-group mt-1 mb-1" id="<?php echo e($originalIndex . $originalTopicIndex); ?>">
            <span class="btn btn-outline-light btn-remove-topic" data-target="<?php echo e($originalIndex . $originalTopicIndex); ?>" title="Remover Tópico"><i class="ri-delete-bin-line text-danger text-opacity-50"></i></span>

            <input type="text" class="form-control" name="[<?php echo e($originalIndex); ?>]['topicData'][<?php echo e($originalTopicIndex); ?>]['topic_name']" value="<?php echo e($topicName); ?>" maxlength="100" title="Exemplo: Este setor/departamento está organizado?... O abastecimento de produtos/insumos está em dia?" required>

            <input type="hidden" name="[<?php echo e($originalIndex); ?>]['topicData'][<?php echo e($originalTopicIndex); ?>]['original_position']" value="<?php echo e($originalPosition); ?>" tabindex="-1">
            <input type="hidden" name="[<?php echo e($originalIndex); ?>]['topicData'][<?php echo e($originalTopicIndex); ?>]['new_position']" value="<?php echo e($newPosition); ?>" tabindex="-1">

            <span class="btn btn-outline-light cursor-n-resize handle-receiver-block" title="Reordenar"><i class="ri-arrow-up-down-line text-body"></i></span>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views/audits/compose/components/topicData-form.blade.php ENDPATH**/ ?>