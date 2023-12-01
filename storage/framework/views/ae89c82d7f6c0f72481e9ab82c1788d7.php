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
                Compor Modelo de Formulário
            <?php endif; ?>
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <?php echo $__env->make('components.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php
        //appPrintR($data);
        $templateId = $data->id ?? '';
        $authorId = $data->user_id ?? '';
        $title = $data->title ?? '';
        $description = $data->description ?? '';

        $countSurveys = $data && is_array($data) ? count($data) : 0;
        $countSurveysText = $countSurveys > 1 ? 'Este modelo está sendo utilizado em '.$countSurveys.' vistorias. A edição deste não influênciará nos dados das rotinas que estão em andamento.' : 'Este modelo está sendo utilizado em uma vistoria. A edição deste não influênciará nos dados da rotina que está em andamento.';
        $countSurveysText .= '<br><br>Se a intenção for a de modificar tópicos dos processos em andamento, não será possível devido ao armazenamento de informações para comparativo. Portanto, o caminho ideal será encerrar determinada vistoria e gerar um novo registro. Se este for o caso, prossiga com a edição deste modelo e reutilize-o gerando uma nova tarefa.'
    ?>

    <?php if( $authorId && $authorId != auth()->id()): ?>
        <div class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show mt-4" role="alert">
            <i class="ri-alert-line label-icon blink"></i> Você não possui autorização para editar um registro gerado por outra pessoa
        </div>
    <?php else: ?>
        <?php if($countSurveys): ?>
            <div class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show" role="alert">
                <i class="ri-alert-line label-icon"></i> <?php echo $countSurveysText; ?>

            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <div class="float-end">

                </div>
                <h4 class="card-title mb-0"><i class="ri-survey-line fs-16 align-middle text-theme me-2"></i><?php echo e($data->title ?? 'Formulário'); ?></h4>
            </div>
            <div class="card-body">
                <form id="surveyTemplateForm" method="POST" class="needs-validation" novalidate autocomplete="off">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-6 col-xxl-6">
                            <div class="p-3">
                                <p class="text-body fw-bold mb-4">Composição do Modelo</p>

                                <input type="hidden" name="id" value="<?php echo e($templateId); ?>">

                                <div class="mb-4">
                                    <label for="title" class="form-label">Título:</label>
                                    <input type="text" id="title" name="title" class="form-control" value="<?php echo e($title); ?>" maxlength="100" required>
                                    <div class="form-text">Exemplo: Checklist Abertura de Loja</div>
                                </div>

                                <div>
                                    <label for="description" class="form-label">Descrição:</label>
                                    <textarea name="description" class="form-control maxlength" id="description" rows="3" maxlength="500" placeholder="Descreva, por exemplo, a funcionalidade ou destino deste modelo..."><?php echo e($description); ?></textarea>
                                    <div class="form-text">Opcional</div>
                                </div>

                                <hr class="w-50 start-50 position-relative translate-middle-x clearfix mt-4 mb-4">

                                <div id="nested-compose-area" style="min-height: 250px;">
                                    <div class="accordion list-group nested-list nested-sortable-block"><?php if($result): ?>
                                        <?php echo $__env->make('surveys.templates.form', ['data' => $result] , \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    <?php endif; ?></div>

                                    <div class="clearfix mt-3">
                                        <?php if($data): ?>
                                            <button type="button" class="btn btn-label right btn-theme float-end mt-5" id="btn-survey-template-store-or-update" tabindex="-1"><i class="ri-save-3-line label-icon align-middle fs-16 ms-2"></i>Atualizar Formulário</button>

                                            

                                            
                                        <?php else: ?>
                                            <button type="button" class="btn btn-label right btn-theme float-end mt-5" id="btn-survey-template-store-or-update" tabindex="-1"><i class="ri-save-3-line label-icon align-middle fs-16 ms-2"></i>Salvar Formulário</button>
                                        <?php endif; ?>

                                        <button type="button" class="btn btn-sm btn-outline-theme btn-label right" data-bs-toggle="modal" data-bs-target="#addStepModal" tabindex="-1" title="Adicionar Etapa/Setor/Departamento"><i class="ri-terminal-window-line label-icon align-middle fs-16 ms-2"></i>Adicionar Bloco</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6 col-xxl-6">
                            <div id="load-preview" class="p-3 border border-1 border-light rounded"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div id="addStepModal" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-right">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalgridLabel">Termos</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="load-terms-form">
                            <?php echo $__env->make('surveys.terms.form', ['terms' => $terms] , \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>

    <script src="<?php echo e(URL::asset('build/libs/sortablejs/Sortable.min.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('build/libs/flatpickr/flatpickr.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/flatpickr/l10n/pt.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/flatpickr/plugins/monthSelect/index.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/flatpickr/plugins/confirmDate/confirmDate.js')); ?>"></script>

    <script>
        var surveysIndexURL = "<?php echo e(route('surveysIndexURL')); ?>";
        var surveysTemplateEditURL = "<?php echo e(route('surveysTemplateEditURL')); ?>";
        var surveysTemplateShowURL = "<?php echo e(route('surveysTemplateShowURL')); ?>";
        var surveysTemplateStoreOrUpdateURL = "<?php echo e(route('surveysTemplateStoreOrUpdateURL')); ?>";

        var surveysTermsStoreOrUpdateURL = "<?php echo e(route('surveysTermsStoreOrUpdateURL')); ?>";
        var surveysTermsFormURL = "<?php echo e(route('surveysTermsFormURL')); ?>";
    </script>
    <script src="<?php echo e(URL::asset('build/js/surveys-templates.js')); ?>" type="module"></script>

    <script src="<?php echo e(URL::asset('build/js/surveys-sortable.js')); ?>" type="module"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/surveys/templates/create.blade.php ENDPATH**/ ?>