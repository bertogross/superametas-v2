<?php $__env->startSection('title'); ?>
    Composição
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('url'); ?>
            <?php echo e(route('auditsComposeIndexURL')); ?>

        <?php $__env->endSlot(); ?>
        <?php $__env->slot('li_1'); ?>
            Composições
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            <?php if($compose): ?>
                Edição de Formulário
            <?php else: ?>
                Compor Formulário
            <?php endif; ?>
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

        <?php echo $__env->make('components.alert-errors', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <?php echo $__env->make('components.alert-success', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div class="row mb-3">
            <div class="col-md-12 col-lg-6 col-xxl-7">
                <div class="card h-100">
                    <form id="auditsComposeForm" method="POST" autocomplete="off" class="needs-validation" novalidate>
                        <?php echo csrf_field(); ?>

                        <input type="hidden" name="id" value="<?php echo e($compose->id ?? ''); ?>">

                        <div class="card-header">
                            <div class="float-end">
                                <?php if($compose): ?>
                                    <button type="button" class="btn btn-sm btn-outline-theme" id="btn-audits-compose-store-or-update" tabindex="-1">Atualizar</button>

                                    <?php if($compose->status == 'active'): ?>
                                        <button type="button" class="btn btn-sm btn-outline-warning" id="btn-audits-compose-toggle-status" data-status-to="disabled" data-compose-id="<?php echo e($compose->id); ?>" tabindex="-1">Desativar</button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger" id="btn-audits-compose-toggle-status" data-status-to="active" data-compose-id="<?php echo e($compose->id); ?> tabindex="-1">Ativar</a>
                                    <?php endif; ?>

                                    <button type="button" class="btn btn-sm btn-outline-info" id="btn-audits-compose-clone" tabindex="-1">Clonar</button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-sm btn-theme" id="btn-audits-compose-store-or-update" tabindex="-1">Salvar</button>
                                <?php endif; ?>
                            </div>

                            <h4 class="card-title mb-0"><i class="ri-drag-drop-line fs-16 align-middle text-theme me-2"></i>Formulário</h4>
                        </div>

                        <div id="nested-compose-area" class="card-body pb-0" style="min-height: 250px;">
                            <p class="text-muted">
                                Esta é a área de composição
                            </p>
                            <div class="form-floating">
                                <input type="text" name="title" class="form-control" id="floatingInput" value="<?php echo e($compose ? $compose->title : ''); ?>" required autocomplete="off" maxlength="100">
                                <label for="floatingInput">Título do Formulário</label>
                            </div>
                            <div class="form-text">Título é necessário para que, quando na listagem, você facilmente identifique este modelo</div>

                            <div class="accordion list-group nested-list nested-receiver rounded rounded-2 p-0 mt-3"><?php if($jsondata): ?>
                                <?php $__currentLoopData = $jsondata; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stepIndex => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $data = $step['stepData'];
                                        $stepName = $data['step_name'] ?? '';
                                        $originalPosition = $data['original_position'] ?? '';
                                        $originalIndex = intval($originalPosition);
                                        $newPosition = $data['new_position'] ?? '';
                                    ?>
                                    <div id="<?php echo e($originalIndex); ?>" class="accordion-item block-item mt-0 mb-3 border-dark p-0">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="[<?php echo e($originalIndex); ?>]['stepData']['step_name']" value="<?php echo e($stepName); ?>" autocomplete="off" maxlength="100" required>

                                            <input type="hidden" name="[<?php echo e($originalIndex); ?>]['stepData']['original_position']" value="<?php echo e($originalPosition); ?>" tabindex="-1">
                                            <input type="hidden" name="[<?php echo e($originalIndex); ?>]['stepData']['new_position']" value="<?php echo e($newPosition); ?>" tabindex="-1">

                                            <span class="btn btn-outline-light cursor-n-resize handle-receiver" title="Reordenar"><i class="ri-arrow-up-down-line text-body"></i></span>

                                            <span class="btn btn-outline-light btn-accordion-toggle"><i class="ri-arrow-up-s-line"></i></span>
                                        </div>
                                        <div class="accordion-collapse collapse show">
                                            <div class="nested-receiver-block border-1 border-dashed border-dark mt-0 p-1 rounded-0"><?php if(isset($step['topicData']) && is_array($step['topicData'])): ?>
                                                <?php $__currentLoopData = $step['topicData']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topicIndex => $topic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php
                                                        $topicName = $topic['topic_name'] ?? '';
                                                        $originalPosition = $topic['original_position'] ?? '';
                                                        $originalTopicIndex = intval($originalPosition);
                                                        $newPosition = $topic['new_position'] ?? '';
                                                    ?>
                                                    <div class="input-group mt-1 mb-1" id="<?php echo e($originalIndex . $originalTopicIndex); ?>">
                                                        <span class="btn btn-outline-light btn-remove-topic" data-target="<?php echo e($originalIndex . $originalTopicIndex); ?>" title="Remover Tópico"><i class="ri-delete-bin-line text-danger text-opacity-50"></i></span>

                                                        <input type="text" class="form-control" name="[<?php echo e($originalIndex); ?>]['topicData'][<?php echo e($originalTopicIndex); ?>]['topic_name']" value="<?php echo e($topicName); ?>" maxlength="100" title="Exemplo: Este setor/departamento está organizado?... O abastecimento de produtos/insumos está em dia?">

                                                        <input type="hidden" name="[<?php echo e($originalIndex); ?>]['topicData'][<?php echo e($originalTopicIndex); ?>]['original_position']" value="<?php echo e($originalPosition); ?>" tabindex="-1">
                                                        <input type="hidden" name="[<?php echo e($originalIndex); ?>]['topicData'][<?php echo e($originalTopicIndex); ?>]['new_position']" value="<?php echo e($newPosition); ?>" tabindex="-1">

                                                        <span class="btn btn-outline-light cursor-n-resize handle-receiver-block" title="Reordenar"><i class="ri-arrow-up-down-line text-body"></i></span>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?></div>

                                            <div class="clearfix">
                                                <span class="btn btn-outline-light btn-remove-block float-start" data-target="<?php echo e($originalIndex); ?>" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="right" title="Remover Bloco"><i class="ri-delete-bin-line text-danger text-opacity-50"></i></span>

                                                <span class="btn btn-outline-light btn-add-topic float-end cursor-copy text-theme" data-block-index="<?php echo e($originalIndex); ?>" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Adicionar Tópico"><i class="ri-menu-add-line"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?></div>

                            <div class="clearfix">
                                <button type="button" class="btn btn-sm btn-outline-theme float-end cursor-crosshair" id="btn-add-block" tabindex="-1" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Adicionar Bloco"><i class="ri-folder-add-line"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-12 col-lg-6 col-xxl-5">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="float-end">
                            <a href="<?php echo e(route('auditsComposeShowURL', ['id' => $compose->id])); ?>" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Visualizar em nova guia" target="_blank" tabindex="-1"><i class="ri-eye-line"></i></a>
                        </div>
                        <h4 class="card-title mb-0"><i class="ri-eye-2-fill fs-16 align-middle text-theme me-2"></i>Pré-visualização</h4>
                    </div>

                    <div id="load-preview" class="card-body">
                        <p class="text-center mt-3">Ao clicar em <?php echo e($compose ? 'Atualizar' : 'Salvar'); ?> uma prévia do formulário será exibida aqui</p>
                    </div>
                </div>
            </div>
        </div>

    <?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('build/libs/sortablejs/Sortable.min.js')); ?>"></script>

    <script>
        var auditsComposeShowURL = "<?php echo e(route('auditsComposeShowURL')); ?>";
        var auditsComposeStoreURL = "<?php echo e(route('auditsComposeStoreURL')); ?>";
        var auditsComposeUpdateURL = "<?php echo e(route('auditsComposeUpdateURL')); ?>";
        var auditsComposeToggleStatusURL = "<?php echo e(route('auditsComposeToggleStatusURL')); ?>";
    </script>
    <script src="<?php echo e(URL::asset('build/js/audits-compose.js')); ?>" type="module"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views/audits/compose/create.blade.php ENDPATH**/ ?>