<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.surveys'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('url'); ?>
            <?php echo e(route('surveysIndexURL')); ?>

        <?php $__env->endSlot(); ?>
        <?php $__env->slot('li_1'); ?>
            <?php echo app('translator')->get('translation.surveys'); ?>
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            <?php if($data): ?>
                Edição de Modelo <small><i class="ri-arrow-drop-right-fill text-theme ms-2 me-2 align-bottom"></i> <span class="text-theme"><?php echo e($data->id); ?></span> </small>
            <?php else: ?>
                Compor Modelo
            <?php endif; ?>
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <?php echo $__env->make('components.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php
        $templateId = $data->id ?? '';
        $title = $data->title ?? '';
        $description = $data->description ?? '';
        $recurring = $data->recurring ?? '';

        appPrintR($data);
        appPrintR($default);
        appPrintR($custom);
    ?>

    <div class="card">
        <div class="card-header">
            <div class="float-end">
                <?php if($data): ?>
                    <button type="button" class="btn btn-sm btn-outline-theme" id="btn-survey-template-store-or-update" tabindex="-1">Atualizar</button>

                    <button type="button" class="btn btn-sm btn-outline-info" id="btn-surveys-clone" tabindex="-1">Clonar</button>

                    <a href="<?php echo e(route('surveyTemplateShowURL', ['id' => $data->id])); ?>" class="btn btn-sm btn-outline-dark" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Visualizar em nova guia" target="_blank" tabindex="-1">Visualizar</a>
                <?php else: ?>
                    <button type="button" class="btn btn-sm btn-theme" id="btn-survey-template-store-or-update" tabindex="-1">Salvar</button>
                <?php endif; ?>
            </div>
            <h4 class="card-title mb-0"><i class="ri-drag-drop-line fs-16 align-middle text-theme me-2"></i><?php echo e($data->title ?? 'Formulário'); ?></h4>
         </div>
        <div class="card-body">
            <form id="surveyTemplateForm" method="POST" class="needs-validation" novalidate autocomplete="off">
                <?php echo csrf_field(); ?>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-4 col-xxl-3">
                        <div class="p-3 border border-1 border-light rounded">
                            <input type="hidden" name="id" value="<?php echo e($templateId); ?>">

                            <div class="mb-4">
                                <label for="title" class="form-label">Título:</label>
                                <input type="text" id="title" name="title" class="form-control" value="<?php echo e($title); ?>" maxlength="100" required>
                                <div class="form-text">
                                    Exemplo: Checklist Abertura de Loja
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="date-range-field" class="form-label">Tipo de Recorrência:</label>
                                <select class="form-select" name="recurring" required>
                                    <option disabled selected>- Selecione -</option>
                                    <?php $__currentLoopData = $getSurveyRecurringTranslations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($index); ?>" <?php echo e($recurring == $index ? 'selected' : ''); ?>><?php echo e($value['label']); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>


                            <div>
                                <label for="description" class="form-label">Observações:</label>
                                <textarea name="description" class="form-control maxlength" maxlength="1000" id="description" rows="7" maxlength="500"><?php echo e($description); ?></textarea>
                                <div class="form-text">Opcional</div>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-4 col-xxl-5">
                        <div class="p-3 border border-1 border-light rounded">

                            <p class="text-ody mb-0">Composição do Modelo:</p>

                            <div id="nested-compose-area" style="min-height: 250px;">
                                <div class="accordion list-group nested-list nested-receiver"><?php if($default || $custom): ?>
                                    <?php if($default): ?>
                                        <?php $__env->startComponent('surveys.components.template-form'); ?>
                                            <?php $__env->slot('type', 'default'); ?>
                                            <?php $__env->slot('data', $default); ?>
                                        <?php echo $__env->renderComponent(); ?>
                                    <?php endif; ?>

                                    <?php if($custom): ?>
                                        <?php $__env->startComponent('surveys.components.template-form'); ?>
                                            <?php $__env->slot('type', 'custom'); ?>
                                            <?php $__env->slot('data', $custom); ?>
                                        <?php echo $__env->renderComponent(); ?>
                                    <?php endif; ?>
                                <?php endif; ?></div>

                                <div class="clearfix mt-3">
                                    <button type="button" class="btn btn-sm btn-outline-theme float-end cursor-crosshair" id="btn-add-block" tabindex="-1" title="Adicionar Bloco"><i class="ri-folder-add-line align-middle me-1"></i>Adicionar Bloco</button>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-4 col-xxl-4">
                        <div id="load-preview" class="p-3 border border-1 border-light rounded"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('build/libs/sortablejs/Sortable.min.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('build/libs/flatpickr/flatpickr.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/flatpickr/l10n/pt.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/flatpickr/plugins/monthSelect/index.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/flatpickr/plugins/confirmDate/confirmDate.js')); ?>"></script>

    <script>
        var surveyTemplateEditURL = "<?php echo e(route('surveyTemplateEditURL')); ?>";
        var surveyTemplateShowURL = "<?php echo e(route('surveyTemplateShowURL')); ?>";
        var surveysTemplateStoreOrUpdateURL = "<?php echo e(route('surveysTemplateStoreOrUpdateURL')); ?>";
    </script>
    <script src="<?php echo e(URL::asset('build/js/surveys-template.js')); ?>" type="module"></script>

    <script src="<?php echo e(URL::asset('build/js/surveys-sortable.js')); ?>" type="module"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views\surveys\template\create.blade.php ENDPATH**/ ?>