<div id="surveysList" class="card h-100">
    <div class="card-header">
        <div class="d-flex align-items-center">
            <h5 class="card-title mb-0 flex-grow-1"><i
                    class="ri-checkbox-line fs-16 align-bottom text-theme me-2"></i>Listagem</h5>
            <div class="flex-shrink-0">
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-sm btn-label right btn-outline-theme float-end waves-effect"
                        <?php if(is_object($templates) && count($templates) > 0): ?> id="btn-surveys-create"
                    <?php else: ?>
                        onclick="alert('Você deverá primeiramente registrar um Modelo');" <?php endif; ?>
                        data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left"
                        title="Adicionar Tarefa de Checklist">
                        <i class="ri-add-line label-icon align-middle fs-16 ms-2"></i>Checklist
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body border border-dashed border-end-0 border-start-0 border-top-0" style="flex: inherit !important;">
        <form action="<?php echo e(route('surveysIndexURL')); ?>" method="get" autocomplete="off">
            <div class="row g-3">

                <div class="col-sm-12 col-md col-lg">
                    <input type="text" class="form-control flatpickr-range" name="created_at" placeholder="- Período -" data-min-date="<?php echo e($firstDate ?? ''); ?>" data-max-date="<?php echo e($lastDate ?? ''); ?>" value="<?php echo e(request('created_at') ?? ''); ?>">
                </div>

                <div class="col-sm-12 col-md col-lg">
                    <select class="form-control form-select" name="status">
                        <option value="">- Status -</option>
                        <?php $__currentLoopData = $getSurveyStatusTranslations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option <?php echo e($key == request('status') ? 'selected' : ''); ?> value="<?php echo e($key); ?>" title="<?php echo e($value['description']); ?>">
                                <?php echo e($value['label']); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="col-sm-12 col-md-auto col-lg-auto wrap-form-btn">
                    <button type="submit" name="filter" value="true" class="btn btn-theme waves-effect w-100 init-loader">
                        <i class="ri-equalizer-fill me-1 align-bottom"></i> Filtrar
                    </button>
                </div>

            </div>
        </form>
    </div>

    <div class="card-body">
        <?php if(!$data || $data->isEmpty()): ?>
            <?php $__env->startComponent('components.nothing'); ?>
                
            <?php echo $__env->renderComponent(); ?>
        <?php else: ?>
            <div class="table-responsive table-card mb-4">
                <table class="table table-sm align-middle table-nowrap mb-0 table-striped" id="tasksTable">
                    <thead class="table-light text-muted text-uppercase">
                        <tr>
                            <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Usuário autor deste registro" width="50"></th>
                            <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Título do modelo que serviu de base para gerar os tópicos desta vistoria">
                                Título
                            </th>
                            <th class="text-center d-none" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="A Data de Registro não é necessáriamente a data de início das tarefas">
                                Registrado em
                            </th>
                            <th class="text-left" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Usários que foram designados para tarefas de Vistoria e Auditoria">
                                Atribuições
                            </th>
                            <th class="text-center" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="A Data de início da rotina">
                                Inicial
                            </th>
                            <th class="text-center" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="A Data final da rotina">
                                Final
                            </th>
                            <th class="text-center" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true" data-bs-placement="top" data-bs-title="Recorrências Possíveis" data-bs-content="<?php echo e(implode('<br>', array_column($getSurveyRecurringTranslations, 'label'))); ?>">
                                Recorrência
                            </th>
                            <th class="text-center">
                                Status
                            </th>
                            <th class="text-center" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Quantidade de Tarefas Concluídas"></th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $survey): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php

                                $authorId = $survey->user_id;

                                $surveyId = $survey->id;

                                $title = $survey->title;

                                $distributedData = $survey->distributed_data;
                                $decodedData = json_decode($distributedData, true);
                                $companies = $decodedData ? array_column($decodedData['surveyor_id'], 'company_id') : null;
                                $companies = $companies ? array_unique($companies) : null;
                                $companyNames = [];
                                if($companies){
                                    foreach ($companies as $company => $companyId){
                                        $companyNames[] = getCompanyNameById($companyId);
                                    }
                                }

                                $surveyStatus = $survey->status;


                                $recurring = $survey->recurring;
                                $recurringLabel = $getSurveyRecurringTranslations[$recurring]['label'];

                                $getSurveyTemplateNameById = getSurveyTemplateNameById($survey->template_id);

                                $countSurveyAssignmentBySurveyId = \App\Models\SurveyAssignments::countSurveyAssignmentBySurveyId($surveyId);

                                $delegation = \App\Models\SurveyAssignments::getAssignmentDelegatedsBySurveyId($surveyId);
                            ?>
                            <tr class="main-row" data-id="<?php echo e($surveyId); ?>">
                                <td>
                                    <div class="avatar-group">
                                        <?php
                                            $avatar = getUserData($authorId)['avatar'];
                                            $name = getUserData($authorId)['name'];
                                        ?>
                                        <div class="avatar-group-item">
                                            <a href="<?php echo e(route('profileShowURL', $authorId)); ?>" class="d-inline-block"
                                                data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top"
                                                title="<?php echo e($name); ?> foi o autor deste registro">
                                                <img src="<?php echo e($avatar); ?>" alt="<?php echo e($name); ?>" class="rounded-circle avatar-xxs" loading="lazy">
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="<?php echo e(ucfirst($title)); ?>">
                                        <?php echo e(limitChars(ucfirst($title), 30)); ?>

                                    </span>

                                    <div class="text-muted small" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="<?php echo e(limitChars(ucfirst($getSurveyTemplateNameById), 200)); ?>">
                                        <strong>Modelo:</strong> <span class="text-body"></span><?php echo e(limitChars(ucfirst($getSurveyTemplateNameById), 30)); ?>

                                    </div>

                                    <div class="text-muted small" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Unidades relacionadas a este registro">
                                        <strong>Unidades:</strong> <span class="text-body"> <?php echo e($companyNames ? implode(', ', $companyNames) : 'Não Informado'); ?></span>
                                    </div>
                                </td>
                                <td class="text-center d-none">
                                    <?php echo e($survey->created_at ? date('d/m/Y H:i', strtotime($survey->created_at)) : '-'); ?>

                                </td>
                                <td>
                                    <?php if($delegation): ?>
                                        <div class="avatar-group float-start">
                                            <?php if(isset($delegation['surveyors']) && !empty($delegation['surveyors'])): ?>
                                                <?php $__currentLoopData = $delegation['surveyors']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php
                                                        $userId = $value['user_id'] ?? null;
                                                        $getUserData = $userId ? getUserData($userId) : null;
                                                        $companyId = $value['company_id'] ?? null;
                                                        $companyName = $companyId ? getCompanyNameById($companyId) : '';
                                                    ?>
                                                    <?php if($userId): ?>
                                                        <a href="<?php echo e(route('profileShowURL', $userId)); ?>" class="avatar-group-item border-1 border-white" data-img="<?php echo e($getUserData['avatar']); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Vistoria: <?php echo e($getUserData['name']); ?> : <?php echo e($companyName); ?>">
                                                            <img src="<?php echo e($getUserData['avatar']); ?>" alt="" class="rounded-circle avatar-xxs">
                                                        </a>
                                                    <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="avatar-group float-start ms-1">
                                            <?php if(isset($delegation['auditors']) && !empty($delegation['auditors'])): ?>
                                                <?php $__currentLoopData = $delegation['auditors']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php
                                                        $userId = $value['user_id'] ?? null;
                                                        $getUserData = $userId ? getUserData($userId) : null;
                                                        $companyId = $value['company_id'] ?? null;
                                                        $companyName = $companyId ? getCompanyNameById($companyId) : '';
                                                    ?>
                                                    <?php if($userId): ?>
                                                        <a href="<?php echo e(route('profileShowURL', $userId)); ?>" class="avatar-group-item border-1 border-secondary" data-img="<?php echo e($getUserData['avatar']); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Auditoria: <?php echo e($getUserData['name']); ?> : <?php echo e($companyName); ?>">
                                                            <img src="<?php echo e($getUserData['avatar']); ?>" alt="" class="rounded-circle avatar-xxs">
                                                        </a>
                                                    <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php echo e($survey->start_at ? date('d/m/Y', strtotime($survey->start_at)) : '-'); ?>

                                </td>
                                <td class="text-center">
                                    <?php echo e($survey->end_in ? date('d/m/Y', strtotime($survey->end_in)) : '-'); ?>

                                </td>
                                <td class="text-center">
                                    <span class="badge badge-border bg-dark-subtle text-body text-uppercase"
                                        data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top"
                                        title="<?php echo e($getSurveyRecurringTranslations[$recurring]['description']); ?>">
                                        <?php echo e($recurringLabel); ?>

                                    </span>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-<?php echo e($getSurveyStatusTranslations[$surveyStatus]['color']); ?>-subtle text-<?php echo e($getSurveyStatusTranslations[$surveyStatus]['color']); ?> text-uppercase"
                                        data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top"
                                        title="<?php echo e($getSurveyStatusTranslations[$surveyStatus]['description']); ?>">
                                        <?php echo e($getSurveyStatusTranslations[$surveyStatus]['label']); ?>

                                        <?php if($surveyStatus == 'started'): ?>
                                            <span class="spinner-border align-top ms-1"></span>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-dark-subtle text-body" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Quantidade de Tarefas Concluídas"><?php echo e($countSurveyAssignmentBySurveyId); ?></span>
                                </td>
                                <td scope="row" class="text-end">
                                    <?php if(in_array($surveyStatus, ['new', 'started', 'stopped'])): ?>
                                        <button type="button" data-survey-id="<?php echo e($survey->id); ?>"
                                            data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top"
                                            class="btn btn-sm btn-label right waves-effect btn-soft-<?php echo e($getSurveyStatusTranslations[$surveyStatus]['color']); ?> btn-surveys-change-status"
                                            data-current-status="<?php echo e($surveyStatus); ?>"
                                            title="<?php echo e($getSurveyStatusTranslations[$surveyStatus]['reverse']); ?>">
                                            <i
                                                class="<?php echo e($getSurveyStatusTranslations[$surveyStatus]['icon']); ?> label-icon align-middle fs-16"></i><?php echo e($getSurveyStatusTranslations[$surveyStatus]['reverse']); ?>

                                        </button>
                                    <?php endif; ?>

                                    <?php if(!in_array($surveyStatus, ['completed', 'filed'])): ?>
                                        <button type="button"
                                            <?php if($authorId != auth()->id()): ?> class="btn btn-sm btn-soft-dark waves-effect ri-edit-line"
                                            onclick="alert('Você não possui autorização para editar um registro gerado por outra pessoa');"
                                        <?php else: ?>
                                            class="btn btn-sm btn-soft-dark waves-effect btn-surveys-edit ri-edit-line"
                                            data-survey-id="<?php echo e($surveyId); ?>" <?php endif; ?>
                                            data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left"
                                            title="Editar"></button>
                                    <?php endif; ?>

                                    <?php if( !in_array($surveyStatus, ['scheduled', 'new']) ): ?>
                                        <a
                                            <?php if($countSurveyAssignmentBySurveyId > 0): ?>
                                                href="<?php echo e(route('surveysShowURL', $surveyId)); ?>"
                                            <?php else: ?>
                                                onclick="alert('Não há dados para relatório')"
                                            <?php endif; ?>
                                            class="btn btn-sm btn-soft-dark waves-effect ri-line-chart-fill <?php echo e($countSurveyAssignmentBySurveyId == 0 ? 'cursor-not-allowed' : ''); ?>"
                                            data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left"
                                            title="Visualização Analítica"></a>
                                    <?php endif; ?>
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
</div>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/surveys/listing.blade.php ENDPATH**/ ?>