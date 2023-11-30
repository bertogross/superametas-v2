<?php $__env->startSection('title'); ?>
    Formulários de Vistoria
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
            Formulários de Vistoria
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div id="composeList" class="card">
        <ul class="nav nav-tabs nav-tabs-custom nav-theme nav-justified" role="tablist">
            <li class="nav-item">
                <a class="nav-link text-uppercase active" data-bs-toggle="tab" href="#departments" role="tab">
                    Departamentos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-uppercase" data-bs-toggle="tab" href="#custom" role="tab">
                    Customizados
                </a>
            </li>
        </ul>
        <div class="tab-content text-muted">
            <div class="tab-pane active" id="departments" role="tabpanel">
                <?php if($default->isEmpty()): ?>
                    <?php $__env->startComponent('components.nothing'); ?>
                        <?php $__env->slot('url', route('surveysComposeCreateURL', ['type'=>'default'])); ?>
                    <?php echo $__env->renderComponent(); ?>
                <?php else: ?>
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0  me-2">Departamentos</h4>
                            <div class="flex-shrink-0 ms-auto">
                                <a class="btn btn-outline-theme" href="<?php echo e(route('surveysComposeCreateURL', ['type'=>'default'])); ?>" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Adicionar Formulário Departamentos">
                                    <i class="ri-add-line align-bottom me-1"></i>Departamentos
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap mb-0 table-striped table-hover" id="composeTable">
                                    <thead class="table-light text-muted text-uppercase">
                                        <tr>
                                            <th>ID</th>
                                            <th>Título</th>
                                            <th>Registrado</th>
                                            <th>Atualizado</th>
                                            <th>Status</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $default; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($data->id); ?></td>
                                                <td>
                                                    <?php echo e($data->title); ?>

                                                </td>
                                                <td>
                                                    <?php echo e($data->created_at ? \Carbon\Carbon::parse($data->created_at)->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY - HH:mm:ss') . 'hs' : '-'); ?>

                                                </td>
                                                <td>
                                                    <?php echo e($data->updated_at ? \Carbon\Carbon::parse($data->updated_at)->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY - HH:mm:ss') . 'hs' : '-'); ?>

                                                </td>
                                                <td>
                                                    <?php echo statusBadge($data->status); ?>

                                                </td>
                                                <td scope="row" class="text-end">
                                                    <div class="btn-group">
                                                        <a href="<?php echo e(route('surveysComposeEditURL', $data->id)); ?>" class="btn btn-sm btn-outline-dark" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-line"></i></a>

                                                        <a href="<?php echo e(route('surveysComposeShowURL', $data->id)); ?>" class="btn btn-sm btn-outline-dark" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Visualizar"><i class="ri-eye-line"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="tab-pane" id="custom" role="tabpanel">
                <?php if($custom->isEmpty()): ?>
                    <?php $__env->startComponent('components.nothing'); ?>
                        <?php $__env->slot('url', route('surveysComposeCreateURL', ['type'=>'custom'])); ?>
                    <?php echo $__env->renderComponent(); ?>
                <?php else: ?>
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0  me-2">Customizados</h4>
                            <div class="flex-shrink-0 ms-auto">
                                <a class="btn btn-outline-theme" href="<?php echo e(route('surveysComposeCreateURL', ['type'=>'custom'])); ?>" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Adicionar Formulário Customizado">
                                    <i class="ri-add-line align-bottom me-1"></i>Customizado
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap mb-0 table-striped table-hover" id="composeTable">
                                    <thead class="table-light text-muted text-uppercase">
                                        <tr>
                                            <th>ID</th>
                                            <th>Título</th>
                                            <th>Registrado</th>
                                            <th>Atualizado</th>
                                            <th>Status</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $custom; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($data->id); ?></td>
                                                <td>
                                                    <?php echo e($data->title); ?>

                                                </td>
                                                <td>
                                                    <?php echo e($data->created_at ? \Carbon\Carbon::parse($data->created_at)->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY - HH:mm:ss') . 'hs' : '-'); ?>

                                                </td>
                                                <td>
                                                    <?php echo e($data->updated_at ? \Carbon\Carbon::parse($data->updated_at)->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY - HH:mm:ss') . 'hs' : '-'); ?>

                                                </td>
                                                <td>
                                                    <?php echo statusBadge($data->status); ?>

                                                </td>
                                                <td scope="row" class="text-end">
                                                    <div class="btn-group">
                                                        <a href="<?php echo e(route('surveysComposeEditURL', $data->id)); ?>" class="btn btn-sm btn-outline-dark" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-line"></i></a>

                                                        <a href="<?php echo e(route('surveysComposeShowURL', $data->id)); ?>" class="btn btn-sm btn-outline-dark" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Visualizar"><i class="ri-eye-line"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views\surveys\_deprecated\compose\listing.blade.php ENDPATH**/ ?>