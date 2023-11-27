<div id="surveysList" class="card h-100">
    <div class="card-header">
        <div class="d-flex align-items-center">
            <h5 class="card-title mb-0 flex-grow-1"><i class="ri-survey-line fs-16 align-bottom text-theme me-2"></i>Vistorias</h5>
            <div class="flex-shrink-0">
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-sm btn-label right btn-outline-theme float-end waves-effect" id="btn-surveys-create" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Adicionar Tarefa de Vistoria">
                        <i class="ri-add-line label-icon align-middle fs-16 ms-2"></i>Vistoria
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body border border-dashed border-end-0 border-start-0 border-top-0">
        <form action="<?php echo e(route('surveysIndexURL')); ?>" method="get" autocomplete="off">
            <div class="row g-3">
                
                

                <div class="col-sm-12 col-md col-lg">
                    <input type="text" class="form-control flatpickr-range" name="created_at" placeholder="Período" data-min-date="<?php echo e($firstDate); ?>" data-max-date="<?php echo e($lastDate); ?>" value="<?php echo e(request('created_at')); ?>">
                </div>

                <div class="col-sm-12 col-md col-lg">
                    <div class="input-light">
                        <select class="form-control form-select" name="status">
                            <option value="" <?php echo e(!request('status') ? 'selected' : ''); ?>>Status</option>
                            <?php $__currentLoopData = $getSurveyStatusTranslations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>" <?php echo e(request('status') == $key ? 'selected' : ''); ?>><?php echo e($value['label']); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="col-sm-12 col-md-auto col-lg-auto wrap-form-btn">
                    <button type="submit" name="filter" value="true" class="btn btn-theme waves-effect w-100 init-loader"> <i class="ri-equalizer-fill me-1 align-bottom"></i> Filtrar</button>
                </div>

            </div>
        </form>
    </div>

    <div class="card-body">
        <?php if( !$data || $data->isEmpty() ): ?>
            <?php $__env->startComponent('components.nothing'); ?>
                
            <?php echo $__env->renderComponent(); ?>
        <?php else: ?>
            <div class="table-responsive table-card mb-4">
                <table class="table align-middle table-nowrap mb-0 table-striped" id="tasksTable">
                    <thead class="table-light text-muted text-uppercase">
                        <tr>
                            <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Título do modelo que serviu de base para gerar os tópicos desta vistoria">Modelo</th>
                            <th class="text-center" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Data de Registro não é necessáriamente a data de início das vistorias">Registro</th>
                            
                            <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Colaboradores que receberam a tarefa de Vistoria">Vistoriadores</th>
                            <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Colaboradores que receberam a tarefa de Auditoria">Auditores</th>
                            <th class="text-center" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true" data-bs-placement="top" data-bs-title="Recorrências Possíveis" data-bs-content="<?php echo e(implode('<br>', array_column($getSurveyRecurringTranslations, 'label'))); ?>">Recorrência</th>
                            <th class="text-center" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true" data-bs-placement="top" data-bs-title="Status Possíveis" data-bs-content="<?php echo e(implode('<br>', array_column($getSurveyStatusTranslations, 'label'))); ?>">Status</th>
                            
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $survey): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $surveyId = $survey->id;

                                checkSurveyAssignmentUntilYesterday($surveyId);

                                startNewAssignmentIfSurveyIsRecurring($surveyId);

                                $distributedData = $survey->distributed_data;
                                $decodedData = json_decode($distributedData, true);

                                $surveyStatus = $survey->status;

                                $delegatedToIds = [];
                                $delegatedTo = array_map(function($item) {
                                    return $item['user_id'];
                                }, $decodedData['delegated_to']);
                                $delegatedToIds = count($delegatedTo) > 1 ? array_unique($delegatedTo) : $delegatedTo;

                                $auditedByIds = [];
                                $auditedBy = array_map(function($item) {
                                    return $item['user_id'];
                                }, $decodedData['audited_by']);
                                $auditedByIds = count($auditedBy) > 1 ? array_unique($auditedBy) : $auditedBy;

                                $recurring = $survey->recurring;
                                $recurringLabel = $getSurveyRecurringTranslations[$recurring]['label'];

                                $getTemplateNameById = getTemplateNameById($survey->template_id);
                            ?>
                            <tr class="main-row" data-id="<?php echo e($surveyId); ?>">
                                <td title="<?php echo e($getTemplateNameById); ?>">
                                    <?php echo e(limitChars($getTemplateNameById, 30)); ?>

                                </td>
                                <td class="text-center">
                                    <?php echo e($survey->created_at ? date("d/m/Y", strtotime($survey->created_at)) : '-'); ?>

                                </td>
                                <td>
                                    <?php if($delegatedToIds): ?>
                                        <div class="avatar-group">
                                            <?php $__currentLoopData = $delegatedToIds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $avatar = getUserData($userId)['avatar'];
                                                    $name = getUserData($userId)['name'];
                                                ?>
                                                <div class="avatar-group-item">
                                                    <a href="<?php echo e(route('profileShowURL', $userId)); ?>" class="d-inline-block" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="<?php echo e($name); ?>">
                                                        <img
                                                        <?php if( empty(trim($avatar)) ): ?>
                                                            src="<?php echo e(URL::asset('build/images/users/user-dummy-img.jpg')); ?>"
                                                        <?php else: ?>
                                                            src="<?php echo e(URL::asset('storage/' .$avatar )); ?>"
                                                        <?php endif; ?>
                                                        alt="<?php echo e($name); ?>" class="rounded-circle avatar-xxs">
                                                    </a> 
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($auditedByIds): ?>
                                        <div class="avatar-group">
                                            <?php $__currentLoopData = $auditedByIds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $avatar = getUserData($userId)['avatar'];
                                                    $name = getUserData($userId)['name'];
                                                ?>
                                                <div class="avatar-group-item">
                                                    <a href="<?php echo e(route('profileShowURL', $userId)); ?>" class="d-inline-block" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" aria-label="<?php echo e($name); ?>" title="Clique para visualizar tarefas de <?php echo e($name); ?> em nova guia/janela" target="_blank">
                                                        <img
                                                        <?php if( empty(trim($avatar)) ): ?>
                                                            src="<?php echo e(URL::asset('build/images/users/user-dummy-img.jpg')); ?>"
                                                        <?php else: ?>
                                                            src="<?php echo e(URL::asset('storage/' .$avatar )); ?>"
                                                        <?php endif; ?>
                                                        alt="<?php echo e($name); ?>" class="rounded-circle avatar-xxs">
                                                    </a> 
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-<?php echo e($getSurveyRecurringTranslations[$recurring]['color']); ?>-subtle text-<?php echo e($getSurveyRecurringTranslations[$recurring]['color']); ?> text-uppercase" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="<?php echo e($getSurveyRecurringTranslations[$recurring]['description']); ?>">
                                     <?php echo e($recurringLabel); ?>

                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-<?php echo e($getSurveyStatusTranslations[$surveyStatus]['color']); ?>-subtle text-<?php echo e($getSurveyStatusTranslations[$surveyStatus]['color']); ?> text-uppercase" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="<?php echo e($getSurveyStatusTranslations[$surveyStatus]['description']); ?>">
                                        <?php echo e($getSurveyStatusTranslations[$surveyStatus]['label']); ?>

                                        <?php if($surveyStatus == 'started'): ?>
                                            <span class="spinner-border align-top ms-1"></span>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                
                                <td scope="row" class="text-end">
                                    <?php if( in_array($surveyStatus, ['new', 'started', 'stopped']) ): ?>
                                        <button type="button" data-survey-id="<?php echo e($survey->id); ?>"
                                            data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top"
                                            class="btn btn-sm btn-label right waves-effect btn-soft-<?php echo e($getSurveyStatusTranslations[$surveyStatus]['color']); ?> btn-surveys-change-status"
                                            data-purpose="<?php echo e($getSurveyStatusTranslations[$surveyStatus]['purpose']); ?>"
                                            title="<?php echo e($getSurveyStatusTranslations[$surveyStatus]['reverse']); ?>">
                                                <i class="<?php echo e($getSurveyStatusTranslations[$surveyStatus]['icon']); ?> label-icon align-middle fs-16 ms-2"></i> <?php echo e($getSurveyStatusTranslations[$surveyStatus]['reverse']); ?>

                                        </button>
                                    <?php endif; ?>

                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-soft-dark waves-effect btn-surveys-edit ri-edit-line" data-survey-id="<?php echo e($surveyId); ?>" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Editar"></button>

                                        <a href="<?php echo e(route('surveysShowURL', $surveyId)); ?>" class="btn btn-sm btn-soft-dark waves-effect ri-line-chart-fill" target="_blank" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Visualizar Dados dos Processos de Vistoria em nova Janela"></a>

                                        <button type="button" class="btn btn-sm btn-soft-dark waves-effect btn-toggle-row-detail" data-id="<?php echo e($surveyId); ?>" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Expand/Collapse this row">
                                            <i class="ri-folder-line"></i>
                                            <i class="ri-folder-open-line d-none"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr class="details-row d-none bg-body-tertiary" data-details-for="<?php echo e($surveyId); ?>">
                                <td colspan="9">
                                    <div class="load-row-content" data-survey-id="<?php echo e($surveyId); ?>">
                                        <?php $__env->startComponent('surveys.layouts.listing-row-cards'); ?>
                                            <?php $__env->slot('survey', $survey); ?>
                                            <?php $__env->slot('distributedData', $decodedData); ?>
                                            <?php $__env->slot('recurringLabel', $recurringLabel); ?>
                                            <?php $__env->slot('getSurveyStatusTranslations', $getSurveyStatusTranslations); ?>
                                        <?php echo $__env->renderComponent(); ?>
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