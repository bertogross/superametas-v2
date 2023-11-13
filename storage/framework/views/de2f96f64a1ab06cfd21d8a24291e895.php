<div id="surveyTemplateListing" class="card">
    <div class="card-header border-0">
        <div class="d-flex align-items-center">
            <h5 class="card-title mb-0 flex-grow-1">Modelos</h5>
            <div class="flex-shrink-0">
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-sm btn-outline-theme float-end" href="<?php echo e(route('surveysTemplateCreateURL')); ?>" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Adicionar Modelo">
                        <i class="ri-add-line align-bottom me-1"></i>Modelo
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <?php if($surveyTemplates->isEmpty()): ?>
            <?php $__env->startComponent('components.nothing'); ?>
                <?php $__env->slot('url', route('surveysTemplateCreateURL')); ?>
            <?php echo $__env->renderComponent(); ?>
        <?php else: ?>
            <div class="table-responsive table-card mb-4">
                <table class="table align-middle table-nowrap mb-0 table-striped table-hover" id="tasksTable">
                    <thead class="table-light text-muted text-uppercase">
                        <tr>
                            <th></th>
                            <th data-sort="created_at" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Data de Registro">Data da Composição</th>
                            <th>Tipo de Recorrência</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $surveyTemplates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td scope="row">
                                    <?php echo e($template->id); ?>

                                </td>
                                <td class="created_at">
                                    <?php echo e($template->created_at ? \Carbon\Carbon::parse($template->created_at)->format('d F, Y') : '-'); ?>

                                </td>
                                <td class="status">
                                    <span class="badge bg-secondary-subtle text-body text-uppercase" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="<?php echo e($getSurveyRecurringTranslations[$template->recurring]['description']); ?>">
                                        <?php echo e($getSurveyRecurringTranslations[$template->recurring]['label']); ?>

                                    </span>
                                </td>
                                <td scope="row" class="text-end">
                                    <div class="btn-group">
                                        <a href="<?php echo e(route('surveyTemplateEditURL', $template->id)); ?>" class="btn btn-sm btn-outline-dark waves-effect" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-line"></i></a>

                                        <a href="<?php echo e(route('surveyTemplateShowURL', $template->id)); ?>" class="btn btn-sm btn-outline-dark waves-effect" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Visualizar"><i class="ri-eye-line"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                <?php echo $surveyTemplates->links('layouts.custom-pagination'); ?>

            </div>
        <?php endif; ?>

    </div>
    <!--end card-body-->
</div>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views\surveys\template\listing.blade.php ENDPATH**/ ?>