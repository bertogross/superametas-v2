<div id="surveyTemplateListing" class="card h-100">
    <div class="card-header">
        <div class="d-flex align-items-center">
            <h5 class="card-title mb-0 flex-grow-1"><i class="ri-file-list-line fs-16 align-bottom text-theme me-2"></i>Modelos</h5>
            <div class="flex-shrink-0">
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-sm btn-label right btn-outline-theme float-end waves-effect" href="<?php echo e(route('surveysTemplateCreateURL')); ?>" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Adicionar Modelo">
                        <i class="ri-add-line label-icon align-middle fs-16 ms-2"></i>Modelo
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php if($templates->isEmpty()): ?>
            <?php $__env->startComponent('components.nothing'); ?>
                
            <?php echo $__env->renderComponent(); ?>
        <?php else: ?>
            <div class="row">
                <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-sm-12 col-xl-12 col-sm-6">
                        <div class="card card-animate bg-info-subtle shadow-none bg-opacity-10">
                            <div class="position-absolute start-0" style="z-index: 0;">
                                <svg version="1.2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 120" width="100%" height="93">
                                    <style>
                                        .s0 {
                                            opacity: .05;
                                            fill: var(--vz-success)
                                        }
                                    </style>
                                    <path id="Shape 8" class="s0" d="m189.5-25.8c0 0 20.1 46.2-26.7 71.4 0 0-60 15.4-62.3 65.3-2.2 49.8-50.6 59.3-57.8 61.5-7.2 2.3-60.8 0-60.8 0l-11.9-199.4z"></path>
                                </svg>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="<?php echo e(limitChars(ucfirst($template->title), 200)); ?>">
                                        <p class="text-uppercase fw-medium text-body text-truncate mb-0"><?php echo e(limitChars(ucfirst($template->title), 30)); ?></p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="dropdown dropstart me-n2">
                                            <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                                <span class="text-theme fs-18"><i class="ri-more-2-line"></i></span>
                                            </a>
                                            <div class="dropdown-menu">
                                                <li>
                                                    <a
                                                    <?php if($template->user_id != auth()->id()): ?>
                                                        href="javascript:void(0);"
                                                        onclick="alert('Você não possui autorização para editar um registro gerado por outra pessoa');"
                                                    <?php else: ?>
                                                        href="<?php echo e(route('surveysTemplateEditURL', $template->id)); ?>"
                                                    <?php endif; ?>
                                                    class="dropdown-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Editar">Editar</a>
                                                </li>
                                                <li>
                                                    <a href="<?php echo e(route('surveysTemplatePreviewURL', $template->id)); ?>" class="dropdown-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Visualizar Modelo">Visualizar</a>
                                                </li>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mt-2">
                                    <div class="flex-shrink-0 avatar-xxs text-muted">
                                        <?php
                                            $avatar = getUserData($template->user_id)['avatar'];
                                            $name = getUserData($template->user_id)['name'];
                                        ?>
                                        <a href="<?php echo e(route('profileShowURL', $template->user_id)); ?>" class="d-inline-block" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="<?php echo e($name); ?> foi o autor deste registro">
                                            <img src="<?php echo e($avatar); ?>"
                                            alt="<?php echo e($name); ?>" class="rounded-circle avatar-xxs" loading="lazy">
                                        </a>
                                    </div>
                                    <div class="flex-grow-1 text-end">
                                        <span class="fs-12 fw-semibold ff-secondary mb-0" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Data de registro">
                                            <?php echo e(date("d/m/Y", strtotime($template->created_at))); ?>

                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

    </div>
    <!--end card-body-->
</div>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/surveys/templates/listing.blade.php ENDPATH**/ ?>