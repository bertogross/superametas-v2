<h6>Registre um novo Termo ou selecione entre os existentes</h6>

<div class="form-group mt-4">
    <label for="termiInput" class="form-label">Registrar Termo:</label>
    <div class="input-group">
        <input type="text" name="term" class="form-control" id="termiInput" maxlength="90" autocomplete="off">
        <button id="btn-add-survey-term" type="button" class="btn btn-soft-theme" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Adicionar novo Termo"><i class="ri-terminal-window-line"></i></button>
    </div>
</div>

<hr class="w-50 start-50 position-relative translate-middle-x clearfix mt-4 mb-4">

<form id="surveysPopulateTermForm" method="POST" class="needs-validation" novalidate autocomplete="off">
    <?php echo csrf_field(); ?>
    <label class="form-label">Termos: <i class="ri-question-line text-primary non-printable align-top" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-title="Termos Disponíveis" data-bs-content="Os termos aqui listados não foram ainda inseridos na listagem"></i></label>
    <div class="row">
        <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-sm-12 col-md-6 col-lg-6 form-check-container">
                <div class="form-check form-switch form-switch-theme mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" name="step_terms[]" value="<?php echo e($term->id); ?>" id="SwitchCheck<?php echo e($term->id); ?>" required>
                    <label class="form-check-label" for="SwitchCheck<?php echo e($term->id); ?>"><?php echo e($term->name); ?></label>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="wrap-form-btn d-none mt-3">
        <button type="button" id="btn-add-multiple-blocks" class="btn btn-sm btn-outline-theme btn-label right float-end"  data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Os termos selecionados serão adicionados a sua lista" title="Popular a Listagem"><i class="ri-folder-add-line label-icon align-middle fs-16 ms-2"></i>Popular Listagem</button>
    </div>

</form>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views\surveys\terms\form.blade.php ENDPATH**/ ?>