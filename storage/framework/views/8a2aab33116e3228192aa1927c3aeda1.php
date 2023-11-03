<?php $__env->startSection('title'); ?>
    Visualização
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
            Visualização<small><i class="ri-arrow-drop-right-fill text-theme ms-2 me-2 align-bottom"></i> #<span class="text-theme"><?php echo e($compose->id); ?></span> <?php echo e(limitChars($compose->title ?? '', 20)); ?></small>
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div id="content" class="rounded rounded-2 mb-4">
        <div class="bg-warning-subtle position-relative">
            <span class="float-start m-3"><?php echo statusBadge($compose->status); ?></span>

            <?php if(!$preview): ?>
                <a href="<?php echo e(route('auditsComposeEditURL', ['id' => $compose->id])); ?>" class="btn btn-sm btn-light btn-icon waves-effect ms-2 float-end m-3" title="Editar registro: <?php echo e(limitChars($compose->title ?? '', 20)); ?>"><i class="ri-edit-line"></i></a>
            <?php endif; ?>

            <div class="card-body p-5 text-center">
                <h3><?php echo e($compose ? $compose->title : ''); ?></h3>
                <div class="mb-0 text-muted">
                    Atualizado em:
                    <?php echo e($compose->updated_at ? \Carbon\Carbon::parse($compose->updated_at)->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY - HH:mm:ss') . 'hs' : '-'); ?>

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

        <div><?php if( $jsondata ): ?>
                <?php $__currentLoopData = $jsondata; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stepIndex => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $data = $step['stepData'] ?? null;
                        $stepName = $data['step_name'] ?? 0;
                        $originalPosition = $data['original_position'] ?? 0;
                        $newPosition = $data['new_position'] ?? 0;
                    ?>

                    <?php if($data): ?>
                        <div class="card joblist-card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <h5 class="job-title"><?php echo e($stepName); ?></h5>
                                        <p class="company-name text-muted mb-0" title="Pessoa a qual foi delegada esta vistoria">Responsável: </p>
                                    </div>
                                    <div>
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-light rounded">
                                                <img src="<?php echo e(URL::asset('build/images/users/user-dummy-img.jpg')); ?>" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Tarefa delegada ao (Nome do colaborador)" class="avatar-xxs rounded-circle">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--<p class="text-muted job-description"></p>-->
                            </div>
                            <?php if(isset($step['topicData']) && is_array($step['topicData'])): ?>
                                <?php
                                    $index = 0;
                                    $bg = 'bg-opacity-75';
                                ?>
                                <?php $__currentLoopData = $step['topicData']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topicIndex => $topic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $index++;

                                        $bg = $bg == 'bg-opacity-75' ? 'bg-opacity-50' : 'bg-opacity-75';

                                        $topicName = $topic['topic_name'] ?? '';
                                        $originalPosition = $topic['original_position'] ?? 0;
                                        $newPosition = $topic['new_position'] ?? 0;
                                    ?>
                                    <div class="card-footer border-top-dashed bg-dark <?php echo e($bg); ?>">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 text-uppercase pe-2">
                                                <span class="badge bg-light-subtle text-body badge-border text-theme"><?php echo e($index); ?></span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="mb-0"><?php echo e($topicName); ?></h5>
                                                <div class="row mt-3">
                                                    <div class="col-auto">
                                                        <div class="form-check form-switch form-switch-lg form-switch-theme mb-3">
                                                            <input tabindex="-1" class="form-check-input" type="radio" name="compliance" role="switch" id="SwitchCheck<?php echo e($topicIndex); ?>">
                                                            <label class="form-check-label" for="SwitchCheck<?php echo e($topicIndex); ?>">Conforme</label>
                                                        </div>
                                                        <div class="form-check form-switch form-switch-lg form-switch-danger">
                                                            <input tabindex="-1" class="form-check-input" type="radio" name="compliance" role="switch" id="SwitchCheck2<?php echo e($topicIndex); ?>">
                                                            <label class="form-check-label" for="SwitchCheck2<?php echo e($topicIndex); ?>">Não Conforme</label>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="input-group">
                                                            <button tabindex="-1" type="button" class="btn btn-outline-dark waves-effect waves-light ps-1 pe-1 dropdown" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Bater foto"><i  class="ri-image-add-fill fs-5 m-2"></i></button>

                                                            <textarea tabindex="-1" class="form-control" maxlength="1000" rows="3" placeholder="Observações..."></textarea>

                                                            <button tabindex="-1" type="button" class="btn btn-outline-dark waves-effect waves-light"><i  class="ri-save-3-line fs-3 m-2 text-theme" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Salvar"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?></div>

    </div>
    <?php
        // appPrintR($compose->jsondata);
        // appPrintR($jsondata);
    ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views/audits/compose/show.blade.php ENDPATH**/ ?>