<div id="surveysList" class="card h-100">
    <div class="card-header">
        <div class="d-flex align-items-center">
            <h5 class="card-title mb-0 flex-grow-1"><i class="ri-survey-line fs-16 align-bottom text-theme me-2"></i>Vistorias</h5>
            <div class="flex-shrink-0">
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-sm btn-label right btn-outline-theme float-end waves-effect"
                    <?php if( is_object($templates) && count($templates) > 0 ): ?>
                        id="btn-surveys-create"
                    <?php else: ?>
                        onclick="alert('Você deverá primeiramente registrar um Modelo');"
                    <?php endif; ?>
                    data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Adicionar Tarefa de Vistoria">
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
                    <input type="text" class="form-control flatpickr-range" name="created_at" placeholder="Período" data-min-date="<?php echo e($firstDate ?? ''); ?>" data-max-date="<?php echo e($lastDate ?? ''); ?>" value="<?php echo e(request('created_at') ?? ''); ?>">
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
                            <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Usuário autor deste registro" width="50"></th>
                            <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Título do modelo que serviu de base para gerar os tópicos desta vistoria">Modelo</th>
                            <th class="text-center" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Data de Registro não é necessáriamente a data de início das vistorias">Registro</th>
                            
                            
                            <th class="text-center" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true" data-bs-placement="top" data-bs-title="Recorrências Possíveis" data-bs-content="<?php echo e(implode('<br>', array_column($getSurveyRecurringTranslations, 'label'))); ?>">Recorrência</th>
                            <th class="text-center" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true" data-bs-placement="top" data-bs-title="Status Possíveis" data-bs-content="<?php echo e(implode('<br>', array_column($getSurveyStatusTranslations, 'label'))); ?>">Status</th>
                            
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $survey): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $authorId = $survey->user_id;

                                $surveyId = $survey->id;

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
                                <td>
                                    <div class="avatar-group">
                                        <?php
                                            $avatar = getUserData($authorId)['avatar'];
                                            $name = getUserData($authorId)['name'];
                                        ?>
                                        <div class="avatar-group-item">
                                            <a href="<?php echo e(route('profileShowURL', $authorId)); ?>" class="d-inline-block" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="<?php echo e($name); ?> é o autor deste registro">
                                                <img src="<?php echo e($avatar); ?>"
                                                alt="<?php echo e($name); ?>" class="rounded-circle avatar-xxs">
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td title="<?php echo e($getTemplateNameById); ?>">
                                    <?php echo e(limitChars($getTemplateNameById, 30)); ?>

                                </td>
                                <td class="text-center">
                                    <?php echo e($survey->created_at ? date("d/m/Y", strtotime($survey->created_at)) : '-'); ?>

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
                                            data-current-status="<?php echo e($surveyStatus); ?>"
                                            title="<?php echo e($getSurveyStatusTranslations[$surveyStatus]['reverse']); ?>">
                                                <i class="<?php echo e($getSurveyStatusTranslations[$surveyStatus]['icon']); ?> label-icon align-middle fs-16 ms-2"></i> <?php echo e($getSurveyStatusTranslations[$surveyStatus]['reverse']); ?>

                                        </button>
                                    <?php endif; ?>

                                    <div class="btn-group">
                                        <button type="button"
                                        <?php if($authorId != auth()->id()): ?>
                                            class="btn btn-sm btn-soft-dark waves-effect ri-edit-line"
                                            onclick="alert('Você não possui autorização para editar um registro gerado por outra pessoa');"
                                        <?php else: ?>
                                            class="btn btn-sm btn-soft-dark waves-effect btn-surveys-edit ri-edit-line"
                                            data-survey-id="<?php echo e($surveyId); ?>"
                                        <?php endif; ?>
                                        data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Editar"></button>

                                        <a href="<?php echo e(route('surveysShowURL', $surveyId)); ?>" class="btn btn-sm btn-soft-dark waves-effect ri-line-chart-fill" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Visualizar Dados Analíticos"></a>

                                        
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