

<div id="surveysList" class="card h-100">
    <div class="card-header">
        <div class="d-flex align-items-center">
            <h5 class="card-title mb-0 flex-grow-1">Vistorias</h5>
            <div class="flex-shrink-0">
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-sm btn-label right btn-outline-theme float-end" id="btn-surveys-create" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Adicionar Tarefa de Vistoria">
                        <i class="ri-add-line label-icon align-middle fs-16 ms-2"></i>Vistoria
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php if(!$data->isEmpty()): ?>
        <div class="card-body border border-dashed border-end-0 border-start-0 border-top-0">
            <form action="<?php echo e(route('surveysIndexURL')); ?>" method="get" autocomplete="off">
                <div class="row g-3">
                    
                    

                    <div class="col-sm-12 col-md col-lg">
                        <input type="text" class="form-control bg-light border-light flatpickr-range" name="created_at" placeholder="Período" data-min-date="<?php echo e($firstDate); ?>" data-max-date="<?php echo e($lastDate); ?>" value="<?php echo e(request('created_at')); ?>">
                    </div>

                    <div class="col-sm-12 col-md col-lg">
                        <div class="input-light">
                            <select class="form-select" name="status">
                                <option value="" <?php echo e(!request('status') ? 'selected' : ''); ?>>Status</option>
                                <?php $__currentLoopData = $getSurveyStatusTranslations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>" <?php echo e(request('status') == $key ? 'selected' : ''); ?>><?php echo e($value['label']); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-auto col-lg-auto wrap-form-btn">
                        <button type="submit" class="btn btn-theme w-100 init-loader"> <i class="ri-equalizer-fill me-1 align-bottom"></i> Filtrar</button>
                    </div>

                </div>
            </form>
        </div>
    <?php endif; ?>

    <div class="card-body pt-1">
        <?php if($data->isEmpty()): ?>
            <?php $__env->startComponent('components.nothing'); ?>
                
            <?php echo $__env->renderComponent(); ?>
        <?php else: ?>
            <div class="table-responsive table-card mb-4">
                <table class="table align-middle table-nowrap mb-0 table-striped table-hover" id="tasksTable">
                    <thead class="table-light text-muted text-uppercase">
                        <tr>
                            <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Título do modelo que serviu de base para gerar os tópicos desta vistoria">Modelo</th>
                            <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Data de Registro">Início</th>
                            
                            <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Colaborador ao qual foi atribuída a tarefa de Vistoria">Atribuído a</th>
                            <th data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true" data-bs-placement="top" data-bs-title="Status Possíveis" data-bs-content="<?php echo e(implode('<br>', array_column($getSurveyStatusTranslations, 'label'))); ?>">Status</th>
                            
                            <th scope="col" width="25"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $survey): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <?php echo e(limitChars(getTemplateNameById($survey->template_id), 30)); ?>

                                </td>
                                <td>
                                    <?php echo e($survey->start_date ? date("d/m/Y", strtotime($survey->start_date)) : '-'); ?>

                                </td>
                                
                                <td class="align-middle">
                                    <!--
                                    <?php if($survey->delegated_to): ?>
                                        <?php
                                            $avatar = getUserData($survey->delegated_to)['avatar'];
                                            $name = getUserData($survey->delegated_to)['name'];
                                        ?>
                                        <a href="javascript: void(0);" class="avatar-group-item me-1" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" aria-label="<?php echo e($name); ?>" title="<?php echo e($name); ?>">
                                            <img
                                            <?php if( empty(trim($avatar)) ): ?>
                                                src="<?php echo e(URL::asset('build/images/users/user-dummy-img.jpg')); ?>"
                                            <?php else: ?>
                                                src="<?php echo e(URL::asset('storage/' .$avatar )); ?>"
                                            <?php endif; ?>
                                            alt="<?php echo e($name); ?>" class="rounded-circle avatar-xxs">
                                        </a> 
                                    <?php endif; ?>
                                    -->
                                    -
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo e($getSurveyStatusTranslations[$survey->status]['color']); ?>-subtle text-<?php echo e($getSurveyStatusTranslations[$survey->status]['color']); ?> text-uppercase" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="<?php echo e($getSurveyStatusTranslations[$survey->status]['description']); ?>">
                                        <?php echo e($getSurveyStatusTranslations[$survey->status]['label']); ?>

                                    </span>
                                </td>
                                
                                <td scope="row" class="text-end">
                                    <div class="dropdown dropstart">
                                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="text-theme fs-18"><i class="ri-more-2-line"></i></span>
                                        </a>
                                        <div class="dropdown-menu">
                                            <?php if( $survey->status == 'new' ): ?>
                                                <li>
                                                    <a href="javascript:void(0);" class="dropdown-item btn-surveys-edit" data-survey-id="<?php echo e($survey->id); ?>" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Editar">Editar</a>
                                                </li>
                                            <?php else: ?>
                                                <li>
                                                    <a href="javascript:void(0);" disabled class="cursor-not-allowed" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="left" data-bs-title="Edição Bloqueada" data-bs-content="Status <b class='text-<?php echo e($getSurveyStatusTranslations[$survey->status]['color']); ?>'><?php echo e($getSurveyStatusTranslations[$survey->status]['label']); ?></b><br><br>A edição será possível somente se você <b>Interromper</b> esta Tarefa">Editar</a>
                                                </li>
                                            <?php endif; ?>
                                            <li>
                                                <a href="<?php echo e(route('surveysShowURL', $survey->id)); ?>" class="dropdown-item" target="_blank" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Visualizar Vistoria em nova Janela">Visualizar</a>
                                            </li>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                <?php echo $data->links('layouts.custom-pagination'); ?>

            </div>
        <?php endif; ?>

    </div>
    <!--end card-body-->
</div>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/surveys/listing.blade.php ENDPATH**/ ?>