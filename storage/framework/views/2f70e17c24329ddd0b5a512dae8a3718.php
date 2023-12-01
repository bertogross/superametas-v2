<?php
    $authorId = $data && $data->user_id ? $data->user_id : '';
    $surveyId = $data && $data->id ? $data->id : '';
    $templateId = $data && $data->template_id ? $data->template_id : '';
    $startDate = $data ? $data->start_date : null;
        $startDate = $startDate ? date("d/m/Y", strtotime($startDate)) : date('d/m/Y');

    $distributedData = $data->distributed_data ?? null;
        $distributedData = $distributedData ? json_decode($distributedData, true) : '';

    $recurring = $data->recurring ?? '';

    $surveyStatus = $data->status ?? '';

    $alertMessage0 = $data ? '<div class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show" role="alert">
            <i class="ri-alert-line label-icon"></i> Esta interação já foi iniciada e a alteração do Modelo não poderá ser efetuada. Prossiga se necessitar editar as atribuições.<br>
            Se a intenção for a de modificar tópicos dos processos em andamento, não será possível devido ao armazenamento de informações para comparativo. Portanto, o caminho adequado será o de encerrar/arquivar esta atividade e gerar um novo registro.
        </div>' : '';
    $alertMessage1 = $data ? '<div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show" role="alert">
            <i class="ri-alert-line label-icon"></i> Esta interação já foi iniciada e a alteração da Recorrência não poderá ser efetuada. Prossiga se necessitar editar as atribuições.<br>
            Se a intenção for a de modificar a recorrência de processos em andamento, não será possível devido ao armazenamento de informações para comparativo. Portanto, o caminho adequado será o de encerrar/arquivar esta atividade e gerar um novo registro.
        </div>' : '';

    $alertMessage2 = $data && $recurring != 'once' && $countResponses > 0 ? '<div class="alert alert-info alert-dismissible alert-label-icon label-arrow fade show" role="alert">
            <i class="ri-alert-line label-icon"></i> Esta tarefa já está recebendo dados. Portanto, alterações em Atribuições poderão ser efetuadas mas só terão efeito a partir do próxima interação. No caso de tarefas recorrentes, a próxima interação ocorrerá, por exemplo quando Diária, amanhã.
        </div>' : '';

?>
<div class="modal fade" id="surveysModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0">
            <div class="modal-header p-3 bg-soft-info">
                <h5 class="modal-title">
                    <?php if($surveyId): ?>
                        Edição de: <span class="text-theme"><?php echo e(limitChars(getTemplateNameById($templateId), 30)); ?></span>
                    <?php else: ?>
                        Registrando Nova Vistoria
                    <?php endif; ?>
                </h5>
                <button type="button" class="btn-close btn-destroy" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if( $data && $authorId && $authorId != auth()->id()): ?>
                    <div class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show mt-4" role="alert">
                        <i class="ri-alert-line label-icon blink"></i> Você não possui autorização para editar um registro gerado por outra pessoa
                    </div>
                    <?php
                        exit;
                    ?>
                <?php endif; ?>
                <form id="surveysForm" method="POST" class="needs-validation form-steps" novalidate autocomplete="off">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="id" value="<?php echo e($surveyId); ?>">

                    <div class="step-arrow-nav mb-4">
                        <ul class="nav nav-pills custom-nav nav-pills-theme nav-justified" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link active" data-bs-toggle="pill" role="tab"
                                id="steparrow-template-info-tab"
                                data-bs-target="#steparrow-template-info"
                                aria-controls="steparrow-template-info"
                                aria-selected="true" disabled>Modelo</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link" data-bs-toggle="pill" role="tab"
                                id="steparrow-recurring-info-tab"
                                data-bs-target="#steparrow-recurring-info"
                                aria-controls="steparrow-recurring-info"
                                aria-selected="false" disabled>Recorrencia</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link" data-bs-toggle="pill" role="tab"
                                id="steparrow-users-info-tab"
                                data-bs-target="#steparrow-users-info"
                                aria-controls="steparrow-users-info"
                                aria-selected="false" disabled>Atribuições</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="pill"
                                id="steparrow-success-tab"
                                data-bs-target="#steparrow-success"
                                aria-controls="steparrow-success"
                                aria-selected="false" disabled>Finalizado</button>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" role="tabpanel"
                        id="steparrow-template-info"
                        aria-labelledby="steparrow-template-info-tab">

                            <?php if( !$templates || $templates->isEmpty() ): ?>
                                <?php $__env->startComponent('components.nothing'); ?>
                                    <?php $__env->slot('warning', 'Será necessário primeiramente <u>Compor</u> um Formulário <u>Modelo</u>'); ?>
                                <?php echo $__env->renderComponent(); ?>
                            <?php else: ?>
                                <div class="mb-3">
                                    <label for="template-field" class="form-label">Selecione o Modelo:</label>
                                    <?php if($data && $countResponses > 0 && $surveyStatus != 'new'): ?>
                                        <?php echo $alertMessage0; ?>

                                        <input type="hidden" name="template_id" value="<?php echo e($templateId); ?>">
                                    <?php else: ?>
                                        <select name="template_id" id="template-field" class="form-control form-select" required <?php echo e($countResponses > 0 ? 'readonly' : ''); ?>>
                                            <option value="" <?php echo e(!$templateId ? 'selected' : 'disabled'); ?>>- Selecione -</option>
                                            <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($template->id); ?>" <?php echo e(isset($template->id) && $template->id == $templateId ? 'selected' : ''); ?>><?php echo e($template->title ? e($template->title) : ''); ?> <?php echo e($template->recurring ? ' [ Recorrência: '.$getSurveyRecurringTranslations[$template->recurring]['label'].' ]' : ''); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    <?php endif; ?>
                                </div>

                                <div class="d-flex align-items-start gap-3 mt-4">
                                    <button type="button" class="btn btn-outline-theme btn-label right ms-auto nexttab" data-nexttab="steparrow-recurring-info-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Próximo</button>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="tab-pane fade" role="tabpanel" id="steparrow-recurring-info" aria-labelledby="steparrow-recurring-info-tab">
                            <div class="mb-3">
                                <label for="date-recurring-field" class="form-label">Tipo de Recorrência: <?php echo e($getSurveyRecurringTranslations['$recurring'] ?? ''); ?></label>
                                <?php if($data && $countResponses > 0 && $surveyStatus != 'new'): ?>
                                    <?php echo $alertMessage1; ?>

                                    <input type="hidden" name="recurring" value="<?php echo e($recurring); ?>">
                                <?php else: ?>
                                    <select name="recurring" id="date-recurring-field" class="form-control form-select" required <?php echo e($countResponses > 0 ? 'readonly' : ''); ?>>
                                        <option <?php echo e(!$recurring ? 'selected' : 'disabled'); ?>>- Selecione -</option>
                                        <?php $__currentLoopData = $getSurveyRecurringTranslations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($index); ?>" <?php echo e($recurring == $index ? 'selected' : ''); ?>><?php echo e($value['label']); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex align-items-start gap-3 mt-4">
                                <button type="button" class="btn btn-light btn-label previestab" data-previous="steparrow-template-info-tab"><i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Voltar</button>

                                <button type="button" class="btn btn-outline-theme btn-label right ms-auto nexttab" data-nexttab="steparrow-users-info-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Próximo</button>
                            </div>
                        </div>

                        <div class="tab-pane fade" role="tabpanel"
                        id="steparrow-users-info"
                        aria-labelledby="steparrow-users-info-tab">
                            <?php if( empty($getAuthorizedCompanies) || !is_array($getAuthorizedCompanies) ): ?>
                                <?php $__env->startComponent('components.nothing'); ?>
                                    <?php $__env->slot('warning', 'Será necessário primeiramente solicitar a(o) Administrador(a) a autorização de acesso a Lojas'); ?>
                                <?php echo $__env->renderComponent(); ?>
                            <?php elseif(empty($users)): ?>
                                <?php $__env->startComponent('components.nothing'); ?>
                                    <?php $__env->slot('warning', 'Não há usuários cadastrados/ativos. Consulte o(a) Administrador(a)'); ?>
                                <?php echo $__env->renderComponent(); ?>
                            <?php else: ?>
                                <div>
                                    <label class="form-label mb-0">Atribuições para esta Vistoria:</label>
                                    <div class="form-text mt-0 mb-2">Selecione para cada das Lojas o colaborador(a) que irá proceder com a <strong>Vistoria</strong> e outro(a) na <strong>Auditoria</strong></div>

                                    <?php echo $alertMessage2; ?>

                                    <div class="row">
                                        <div class="col-sm-6 col-md-4 col-lg-4 col-xxl-3">
                                            <div class="nav nav-pills flex-column nav-pills-tab verti-nav-pills custom-verti-nav-pills nav-pills-theme" role="tablist" aria-orientation="vertical">
                                                <?php $__currentLoopData = $getAuthorizedCompanies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $companyId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <a class="nav-link text-uppercase <?php echo e($key == 0 ? 'active' : ''); ?> text-uppercase" data-bs-target="#distributed-tab-company-<?php echo e($companyId); ?>" id="distributed-tab-company-<?php echo e($companyId); ?>-tab" data-bs-toggle="pill" role="tab" aria-controls="v-pills-account" aria-selected="true"><?php echo e(getCompanyNameById($companyId)); ?></a>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-8 col-lg-8 col-xxl-9">
                                            <div class="tab-content p-0 bg-light h-100">
                                                <?php $__currentLoopData = $getAuthorizedCompanies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $companyId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="tab-pane fade show <?php echo e($key == 0 ? 'active' : ''); ?> p-3" id="distributed-tab-company-<?php echo e($companyId); ?>" role="tabpanel" aria-labelledby="distributed-tab-company-<?php echo e($companyId); ?>-tab">
                                                        <div class="row">
                                                            <div class="col mb-3">
                                                                <h6><span class="text-theme">Vistoria</span> Atribuída a:</h6>

                                                                <div class="bg-light p-3 rounded-2">
                                                                    <ul class="list-unstyled vstack gap-2 mb-0">
                                                                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                            <?php
                                                                                $userId = $user->id;
                                                                                $userName = $user->name;
                                                                                $userAvatar = $user->avatar;
                                                                                $userCompanies = getAuthorizedCompanies($userId) ?? null;
                                                                                $isDelegated = false;

                                                                                // Loop through the distributed data to find if this user has been delegated to this company
                                                                                if( $data && isset($distributedData) && is_array($distributedData['delegated_to']) ){
                                                                                    foreach ($distributedData['delegated_to'] as $delegation) {
                                                                                        if ($delegation['company_id'] == $companyId && $delegation['user_id'] == $userId) {
                                                                                            $isDelegated = true;
                                                                                            break;
                                                                                        }
                                                                                    }
                                                                                }
                                                                            ?>
                                                                            <?php if( is_array($userCompanies) && in_array($companyId, $userCompanies) ): ?>
                                                                                <li>
                                                                                    <div class="form-check form-check-success d-flex align-items-center">
                                                                                        <input class="form-check-input me-3" type="radio" name="delegated_to[<?php echo e($companyId); ?>]"
                                                                                            value="<?php echo e($userId); ?>" id="delegated_to-user-<?php echo e($companyId.$userId); ?>" <?php echo e($isDelegated ? 'checked' : ''); ?> required>
                                                                                        <label class="form-check-label d-flex align-items-center"
                                                                                            for="delegated_to-user-<?php echo e($companyId.$userId); ?>">
                                                                                            <span class="flex-shrink-0">
                                                                                                <img
                                                                                                <?php if(empty(trim($userAvatar))): ?>
                                                                                                    src="<?php echo e(URL::asset('build/images/users/user-dummy-img.jpg')); ?>"
                                                                                                <?php else: ?>
                                                                                                    src="<?php echo e(URL::asset('storage/' . $userAvatar)); ?>"
                                                                                                <?php endif; ?>
                                                                                                    alt="<?php echo e($userName); ?>" class="avatar-xxs rounded-circle">
                                                                                            </span>
                                                                                            <span class="flex-grow-1 ms-2"><?php echo e($userName); ?></span>
                                                                                        </label>
                                                                                    </div>
                                                                                </li>
                                                                            <?php endif; ?>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <div class="col mb-3">
                                                                <h6><span class="text-theme">Auditoria</span> Atribuída a:</h6>

                                                                <div class="bg-light p-3 rounded-2">
                                                                    <ul class="list-unstyled vstack gap-2 mb-0">
                                                                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                            <?php
                                                                                $userId = $user->id;
                                                                                $userName = $user->name;
                                                                                $userAvatar = $user->avatar;
                                                                                $userCompanies = getAuthorizedCompanies($userId) ?? null;

                                                                                $isAudited = false;

                                                                                // Loop through the distributed data to find if this user has been delegated to this company
                                                                                if( $data && isset($distributedData) && is_array($distributedData['audited_by']) ){
                                                                                    foreach ($distributedData['audited_by'] as $auditation) {
                                                                                        if ($auditation['company_id'] == $companyId && $auditation['user_id'] == $userId) {
                                                                                            $isAudited = true;
                                                                                            break;
                                                                                        }
                                                                                    }
                                                                                }
                                                                            ?>
                                                                            <?php if( is_array($userCompanies) && in_array($companyId, $userCompanies) ): ?>
                                                                                <li>
                                                                                    <div class="form-check form-check-success d-flex align-items-center">
                                                                                        <input class="form-check-input me-3" type="radio" name="audited_by[<?php echo e($companyId); ?>]"
                                                                                            value="<?php echo e($userId); ?>" id="audited_by-user-<?php echo e($companyId.$userId); ?>" <?php echo e($isAudited ? 'checked' : ''); ?> required>
                                                                                        <label class="form-check-label d-flex align-items-center"
                                                                                            for="audited_by-user-<?php echo e($companyId.$userId); ?>">
                                                                                            <span class="flex-shrink-0">
                                                                                                <img
                                                                                                <?php if(empty(trim($userAvatar))): ?>
                                                                                                    src="<?php echo e(URL::asset('build/images/users/user-dummy-img.jpg')); ?>"
                                                                                                <?php else: ?>
                                                                                                    src="<?php echo e(URL::asset('storage/' . $userAvatar)); ?>"
                                                                                                <?php endif; ?>
                                                                                                    alt="<?php echo e($userName); ?>" class="avatar-xxs rounded-circle">
                                                                                            </span>
                                                                                            <span class="flex-grow-1 ms-2"><?php echo e($userName); ?></span>
                                                                                        </label>
                                                                                    </div>
                                                                                </li>
                                                                            <?php endif; ?>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start gap-3 mt-4">
                                    <button type="button" class="btn btn-light btn-label previestab" data-previous="steparrow-recurring-info-tab"><i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Voltar</button>

                                    <button type="button" class="btn btn-outline-theme btn-label right ms-auto nexttab" data-nexttab="steparrow-success-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Próximo</button>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="tab-pane fade" role="tabpanel"
                        id="steparrow-success"
                        aria-labelledby="steparrow-success-tab">
                            <div class="text-center">
                                <div class="avatar-md mt-5 mb-4 mx-auto">
                                    <div class="avatar-title bg-light text-theme display-4 rounded-circle">
                                        <i class="ri-checkbox-circle-fill"></i>
                                    </div>
                                </div>
                                <h5>Formulário preenchido com sucesso!</h5>
                            </div>

                            <div class="d-flex align-items-start gap-3 mt-4">
                                <button type="button" class="btn btn-light btn-label previestab" data-previous="steparrow-users-info-tab"><i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Voltar</button>

                                <button type="button" id="btn-surveys-store-or-update" class="btn btn-outline-theme btn-label right ms-auto"><i class="ri-save-3-line label-icon align-middle fs-16 ms-2"></i>Salvar</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/surveys/create.blade.php ENDPATH**/ ?>