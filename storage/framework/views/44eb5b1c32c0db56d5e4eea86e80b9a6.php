<?php $__env->startSection('title'); ?>
    Formulário de Vistoria
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('url'); ?>
            <?php echo e(route('surveysComposeIndexURL')); ?>

        <?php $__env->endSlot(); ?>
        <?php $__env->slot('li_1'); ?>
            Formulários
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            <?php if($data): ?>
                Edição <small><i class="ri-arrow-drop-right-fill text-theme ms-2 me-2 align-bottom"></i> #<span class="text-theme"><?php echo e($data->id); ?></span> <?php echo e(limitChars($data->title ?? '', 20)); ?></small>
            <?php else: ?>
                Compor Formulário
            <?php endif; ?>
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

        <?php echo $__env->make('components.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div class="row mb-3">
            <div class="col-md-12 col-lg-6 col-xxl-7">
                <div class="card h-100">
                    <form id="surveysComposeForm" method="POST" autocomplete="off" class="needs-validation" novalidate autocomplete="false">
                        <?php echo csrf_field(); ?>

                        <input type="hidden" name="id" value="<?php echo e($data->id ?? ''); ?>">
                        <input type="hidden" name="type" value="<?php echo e($type ?? 'custom'); ?>">

                        <div class="card-header">
                            <div class="float-end">
                                <button type="button" class="btn btn-sm btn-theme" id="btn-surveys-compose-store-or-update" tabindex="-1">
                                    <?php if($data): ?>
                                        Atualizar
                                    <?php else: ?>
                                        Salvar
                                    <?php endif; ?>
                                </button>
                            </div>

                            <h4 class="card-title mb-0"><i class="ri-drag-drop-line fs-16 align-middle text-theme me-2"></i>Formulário</h4>
                        </div>

                        <div id="nested-compose-area" class="card-body pb-0" style="min-height: 250px;">
                            <div class="row mb-4">
                                <div class="col text-muted">
                                    Esta é a área de composição
                                </div>
                                <div class="col-auto">
                                    <?php if($data): ?>
                                        <span class="btn-group">
                                            <?php if($data->status == 'active'): ?>
                                                <button type="button" class="btn btn-sm btn-outline-danger" id="btn-surveys-compose-toggle-status" data-status-to="disabled" data-compose-id="<?php echo e($data->id); ?>" tabindex="-1">Desativar</button>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-sm btn-outline-success" id="btn-surveys-compose-toggle-status" data-status-to="active" data-compose-id="<?php echo e($data->id); ?>" tabindex="-1">Ativar</a>
                                            <?php endif; ?>

                                            <button type="button" class="btn btn-sm btn-outline-info" id="btn-surveys-compose-clone" tabindex="-1">Clonar</button>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="form-floating">
                                <input type="text" name="title" class="form-control" id="floatingInput" value="<?php echo e($data ? $data->title : ''); ?>" required autocomplete="off" maxlength="100">
                                <label for="floatingInput">Título do Formulário</label>
                            </div>
                            <div class="form-text">Título é necessário para que, quando na listagem, você facilmente identifique este modelo</div>

                            <?php if( !$topicsData && $type == 'default' ): ?>
                                <?php
                                    $defaultTopics = file_get_contents(resource_path('views/surveys/demo/default-survey-topics.json'));
                                    $defaultTopics = json_decode($defaultTopics, true);

                                    $topicsData = [];
                                    foreach($getActiveDepartments as $index => $department){
                                        $topicsData[$index] = [
                                            'stepData' => [
                                                'step_name' => $department->department_alias,
                                                'step_id' => $department->id,
                                                'original_position' => $index,
                                                'new_position' => $index,
                                            ],
                                            'topicData' => $defaultTopics['topics']
                                        ];
                                    }
                                ?>
                            <?php endif; ?>

                            <div class="accordion list-group nested-list nested-receiver rounded rounded-2 p-0 mt-3"><?php if($topicsData): ?>
                                <?php $__currentLoopData = $topicsData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stepIndex => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $stepData = $step['stepData'];
                                        $stepName = $stepData['step_name'] ?? '';
                                        $originalPosition = $stepData['original_position'] ?? $stepIndex;
                                        $originalIndex = intval($originalPosition);
                                        $newPosition = $stepData['new_position'] ?? $stepIndex;
                                    ?>
                                    <div id="<?php echo e($originalIndex); ?>" class="accordion-item block-item mt-0 mb-3 border-dark p-0">
                                        <div class="input-group">
                                            <?php if( $type == 'custom' ): ?>
                                                <input type="text" class="form-control" name="[<?php echo e($originalIndex); ?>]['stepData']['step_name']" value="<?php echo e($stepName); ?>" autocomplete="off" maxlength="100" required>
                                            <?php else: ?>
                                                <input type="text" class="form-control disabled" autocomplete="off" maxlength="100" value="<?php echo e($stepName); ?>"
                                                data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Para Departamentos, este campo não é editável"
                                                readonly disabled>
                                                <input type="hidden" name="[<?php echo e($originalIndex); ?>]['stepData']['step_name']" value="<?php echo e($stepName); ?>">
                                            <?php endif; ?>
                                            <span class="btn btn-outline-light cursor-n-resize handle-receiver" title="Reordenar"><i class="ri-arrow-up-down-line text-body"></i></span>

                                            <span class="btn btn-outline-light btn-accordion-toggle"><i class="ri-arrow-up-s-line"></i></span>
                                        </div>

                                        <input type="hidden" name="[<?php echo e($originalIndex); ?>]['stepData']['original_position']" value="<?php echo e($originalPosition); ?>" tabindex="-1">
                                        <input type="hidden" name="[<?php echo e($originalIndex); ?>]['stepData']['new_position']" value="<?php echo e($newPosition); ?>" tabindex="-1">

                                        <div class="accordion-collapse collapse show">
                                            <?php echo $__env->make('surveys.includes.topics-input', [
                                                'type' => $type,
                                                'topicsData' =>  $step['topicData'],
                                                'originalIndex' => $originalIndex
                                            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?></div>

                            <?php if( $type == 'custom' ): ?>
                                <div class="clearfix">
                                    <button type="button" class="btn btn-sm btn-outline-theme float-end cursor-crosshair" id="btn-add-block" tabindex="-1" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Adicionar Bloco"><i class="ri-folder-add-line"></i></button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-12 col-lg-6 col-xxl-5">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="float-end">
                            <?php if($data): ?>
                                <a href="<?php echo e(route('surveysComposeShowURL', ['id' => is_array($data) ? $data['id'] : $data->id])); ?>" class="btn btn-sm btn-outline-theme position-absolute me-3 end-0" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Visualizar em nova guia" target="_blank" tabindex="-1"><i class="ri-eye-line"></i></a>
                            <?php endif; ?>
                        </div>
                        <h4 class="card-title mb-0"><i class="ri-eye-2-fill fs-16 align-middle text-theme me-2"></i>Pré-visualização</h4>
                    </div>

                    <div id="load-preview" class="card-body">
                        <p class="text-center mt-3">Ao clicar em <?php echo e($data ? 'Atualizar' : 'Salvar'); ?>, uma prévia do formulário será exibida aqui</p>
                    </div>
                </div>
            </div>
        </div>

    <?php $__env->stopSection(); ?>

<?php
    //appPrintR($getActiveDepartments);
    //appPrintR($topicsData);
?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('build/libs/sortablejs/Sortable.min.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('build/js/surveys-sortable.js')); ?>" type="module"></script>

    <script>
        var surveysComposeShowURL = "<?php echo e(route('surveysComposeShowURL')); ?>";
        var surveysComposeCreateOrUpdateURL = "<?php echo e(route('surveysComposeCreateOrUpdateURL')); ?>";
        var surveysComposeToggleStatusURL = "<?php echo e(route('surveysComposeToggleStatusURL')); ?>";
    </script>

    <script src="<?php echo e(URL::asset('build/js/surveys-compose.js')); ?>" type="module"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/surveys/compose/create.blade.php ENDPATH**/ ?>