<?php
/*
$stepData = '{"0":{"stepData":{"item[0][\'step_name\']":"item[0][\'step_name\']","item[0][\'original_position\']":"0","item[0][\'new_position\']":"0"},"topics":[{"item[0][\'step_name\'][\'topic_name\'][0]":"item[0][\'step_name\'][\'topic_name\'][0]","item[0][\'step_name\'][\'topic_name\'][0][\'original_position\']":"0","item[0][\'step_name\'][\'topic_name\'][0][\'new_position\']":"0"},{"item[0][\'step_name\'][\'topic_name\'][1]":"item[0][\'step_name\'][\'topic_name\'][1]","item[0][\'step_name\'][\'topic_name\'][1][\'original_position\']":"1","item[0][\'step_name\'][\'topic_name\'][1][\'new_position\']":"1"},{"item[0][\'step_name\'][\'topic_name\'][2]":"item[0][\'step_name\'][\'topic_name\'][2]","item[0][\'step_name\'][\'topic_name\'][2][\'original_position\']":"2","item[0][\'step_name\'][\'topic_name\'][2][\'new_position\']":"2"}]},"1":{"stepData":{"item[1][\'step_name\']":"item[1][\'step_name\']","item[1][\'original_position\']":"1","item[1][\'new_position\']":"1"},"topics":[{"item[1][\'step_name\'][\'topic_name\'][0]":"item[1][\'step_name\'][\'topic_name\'][0]","item[1][\'step_name\'][\'topic_name\'][0][\'original_position\']":"0","item[1][\'step_name\'][\'topic_name\'][0][\'new_position\']":"0"},{"item[1][\'step_name\'][\'topic_name\'][1]":"item[1][\'step_name\'][\'topic_name\'][1]","item[1][\'step_name\'][\'topic_name\'][1][\'original_position\']":"1","item[1][\'step_name\'][\'topic_name\'][1][\'new_position\']":"1"},{"item[1][\'step_name\'][\'topic_name\'][2]":"item[1][\'step_name\'][\'topic_name\'][2]","item[1][\'step_name\'][\'topic_name\'][2][\'original_position\']":"2","item[1][\'step_name\'][\'topic_name\'][2][\'new_position\']":"2"}]},"2":{"stepData":{"item[2][\'step_name\']":"item[2][\'step_name\']","item[2][\'original_position\']":"2","item[2][\'new_position\']":"2"},"topics":[{"item[2][\'step_name\'][\'topic_name\'][0]":"item[2][\'step_name\'][\'topic_name\'][0]","item[2][\'step_name\'][\'topic_name\'][0][\'original_position\']":"0","item[2][\'step_name\'][\'topic_name\'][0][\'new_position\']":"0"},{"item[2][\'step_name\'][\'topic_name\'][1]":"item[2][\'step_name\'][\'topic_name\'][1]","item[2][\'step_name\'][\'topic_name\'][1][\'original_position\']":"1","item[2][\'step_name\'][\'topic_name\'][1][\'new_position\']":"2"},{"item[2][\'step_name\'][\'topic_name\'][2]":"item[2][\'step_name\'][\'topic_name\'][2]","item[2][\'step_name\'][\'topic_name\'][2][\'original_position\']":"2","item[2][\'step_name\'][\'topic_name\'][2][\'new_position\']":"1"}]},"_token":"ZY9PWHlowYl0C8THSAD4JAJmgSMkUfjDHo1bzMHC","title":"Title"}';
$stepDataArray = json_decode($stepData, true);
echo '<pre>';
print_r($stepDataArray);
echo '</pre>';
*/
?>

<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.audits'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(URL::asset('build/libs/sweetalert2/sweetalert2.min.css')); ?>" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('url'); ?>
            <?php echo e(route('auditsIndexURL')); ?>

        <?php $__env->endSlot(); ?>
        <?php $__env->slot('li_1'); ?>
            <?php echo app('translator')->get('translation.audits'); ?>
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Composição
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

        <?php echo $__env->make('components.alert-errors', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <?php echo $__env->make('components.alert-success', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div class="row mb-3">
            <div class="col-xxl-7">
                <div class="card h-100">
                    <form id="auditsComposeForm" method="POST" autocomplete="off" class="needs-validation" novalidate>
                        <?php echo csrf_field(); ?>

                        <div class="card-header">
                            <div class="btn-group float-end">
                                <button type="button" class="btn btn-sm btn-outline-theme" id="btn-audits-compose-store" tabindex="0">Salvar</button>

                                <a class="btn btn-sm btn-outline-warning" id="tn-audits-compose-disable" tabindex="0">Desativar</a>
                            </div>

                            <h4 class="card-title mb-0"><i class="ri-drag-drop-line fs-16 align-middle text-theme me-2"></i>Formulário</h4>
                        </div>

                        <div id="nested-compose-area" class="card-body pb-0" style="min-height: 250px;">
                            <p class="text-muted">
                                Esta é a área de composição
                            </p>
                            <div class="form-floating">
                                <input type="text" name="title" class="form-control" id="floatingInput" required autocomplete="off" maxlength="100">
                                <label for="floatingInput">Título do Formulário</label>
                            </div>
                            <div class="form-text">Necessário para posterior identificação do modelo</div>

                            <div class="accordion list-group nested-list nested-receiver rounded rounded-1 p-0 mt-3"></div>

                            <div class="clearfix">
                                <button type="button" class="btn btn-sm btn-outline-theme float-end cursor-crosshair" id="btn-add-block" tabindex="0">Adicionar Bloco</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-xxl-5">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="card-title mb-0"><i class="ri-eye-2-fill fs-16 align-middle text-theme me-2"></i>Pré-visualização</h4>
                    </div>

                    <div class="card-body">
                        
                    </div>
                </div>
            </div>
        </div>

    <?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('build/libs/sortablejs/Sortable.min.js')); ?>"></script>

    <script>
        var auditsComposeStoreURL = "<?php echo e(route('auditsComposeStoreURL')); ?>";
        var auditsComposeUpdateURL = "<?php echo e(route('auditsComposeUpdateURL')); ?>";
    </script>
    <script src="<?php echo e(URL::asset('build/js/audits-compose.js')); ?>" type="module"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views/audits/compose.blade.php ENDPATH**/ ?>