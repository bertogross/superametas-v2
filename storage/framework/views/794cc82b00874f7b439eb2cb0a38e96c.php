<?php $__env->startSection('title'); ?>
    Pré-visualização do Modelo de Vistoria
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('url'); ?>
            <?php echo e(route('surveysIndexURL')); ?>

        <?php $__env->endSlot(); ?>
        <?php $__env->slot('li_1'); ?>
            Formulários
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Visualização<small><i class="ri-arrow-drop-right-fill text-theme ms-2 me-2 align-bottom"></i> #<span class="text-theme"><?php echo e($data->id); ?></span> <?php echo e(limitChars($data->title ?? '', 20)); ?></small>
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div id="content" class="rounded rounded-2 mb-4">
        <div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show">
            <i class="ri-alert-line label-icon"></i> Este <strong>não</strong> é um formulário válido.<br>Esta demonstração é exibida durante a Pré-visualização na composição de um Modelo.
        </div>

        <div class="bg-primary-subtle position-relative">
            <?php if(!$edition): ?>
                <span class="float-start m-3 position-absolute"><?php echo statusBadge($data->status); ?></span>
            <?php endif; ?>

            <?php if(!$preview && !$edition): ?>
                <a href="<?php echo e(route('surveysTemplateEditURL', ['id' => $data->id])); ?>" class="btn btn-sm btn-light btn-icon waves-effect ms-2 float-end m-3" title="Editar registro: <?php echo e(limitChars($data->title ?? '', 20)); ?>"><i class="ri-edit-line"></i></a>
            <?php endif; ?>

            <div class="card-body p-5 text-center">
                <h2 class="text-theme text-uppercase">Modelo</h2>
                <h3><?php echo e($data ? $data->title : ''); ?></h3>
                <div class="mb-0 text-muted">
                    Executar em:
                    
                    <?php echo e($data->created_at ? \Carbon\Carbon::parse($data->created_at)->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY') : '-'); ?>

                </div>
            </div>
            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="1440" height="60" preserveAspectRatio="none" viewBox="0 0 1440 60">
                    <g mask="url(&quot;#SvgjsMask1001&quot;)" fill="none">
                        <path d="M 0,4 C 144,13 432,48 720,49 C 1008,50 1296,17 1440,9L1440 60L0 60z" style="fill: var(--vz-secondary-bg);"></path>
                    </g>
                    <defs>
                        <mask id="SvgjsMask1001">
                            <rect width="1440" height="60" fill="#ffffff"></rect>
                        </mask>
                    </defs>
                </svg>
            </div>
        </div>

        <?php if($result): ?>
            <?php $__env->startComponent('surveys.layouts.form-surveyor-step-cards'); ?>
                <?php $__env->slot('data', $result); ?>
                <?php $__env->slot('purpose', 'fakeForm'); ?>
            <?php echo $__env->renderComponent(); ?>
        <?php endif; ?>
    </div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views\surveys\templates\show.blade.php ENDPATH**/ ?>