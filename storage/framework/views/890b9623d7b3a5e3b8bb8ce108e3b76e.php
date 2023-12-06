<?php
    use Carbon\Carbon;
    use App\Models\SurveyResponse;
    use App\Models\SurveyTemplates;
    use App\Models\User;

    $templateData = SurveyTemplates::findOrFail($surveyData->template_id);

    $authorId = $templateData->user_id;
    $getAuthorData = getUserData($authorId);
    $authorRoleName = (new User)->getRoleName($getAuthorData['role']);
    $description = trim($templateData->description) ? nl2br($templateData->description) : '';

    $currentUserId = auth()->id();

    $surveyId = $surveyData->id ?? '';
    $templateName = $surveyData ? getTemplateNameById($surveyData->template_id) : '';

    $assignmentId = $assignmentData->id ?? null;
    $assignmentCreatedAt = $assignmentData->created_at ?? null;

    $auditorStatus = $assignmentData->auditor_status ?? null;
    $auditorId = $assignmentData->auditor_id ?? null;

    $companyId = $assignmentData->company_id ?? '';
    $companyName = $companyId ? getCompanyNameById($companyId) : '';

    $surveyorId = $assignmentData->surveyor_id ?? null;
    $surveyorName = getUserData($surveyorId)['name'];

    $today = Carbon::today();

    $responsesData = SurveyResponse::where('survey_id', $surveyId)
        ->where('assignment_id', $assignmentId)
        //->where('surveyor_id', $surveyorId)
        //->orWhere('auditor_id', $auditorId)
        //->where('company_id', $companyId)
        //->whereDate('created_at', '=', $today)
        ->get()
        ->toArray();
    //appPrintR($responsesData);

?>

<?php $__env->startSection('title'); ?>
    Formulário de Auditoria
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(URL::asset('build/libs/glightbox/css/glightbox.min.css')); ?>">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('url'); ?>
            <?php echo e(route('surveysIndexURL')); ?>

        <?php $__env->endSlot(); ?>
        <?php $__env->slot('li_1'); ?>
            Vistorias
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Auditoria <small><i class="ri-arrow-drop-right-fill text-theme ms-2 me-2 align-bottom"></i> #<span class="text-theme"><?php echo e($surveyId); ?></span> <?php echo e(limitChars($templateName ?? '', 20)); ?></small>
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>
    <div id="content" class="rounded rounded-2 mb-4">
        <div class="bg-secondary-subtle position-relative">
            <div class="card-body p-5 text-center">
                <?php if($companyName ): ?>
                    <h2 class="text-theme text-uppercase"><?php echo e($companyName); ?></h2>
                <?php endif; ?>
                <h2 class="text-secondary">Auditoria</h2>
                <p>A Vistoria foi realizada por <u><?php echo e($surveyorName); ?></u></p>
                <h3><?php echo e($templateName ? ucfirst($templateName) : 'NI'); ?></h3>
                <div class="mb-0 text-muted">
                    Executar em:
                    
                    <?php echo e($assignmentCreatedAt ? \Carbon\Carbon::parse($assignmentCreatedAt)->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY') : '-'); ?>

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
        <?php if($currentUserId != $auditorId): ?>
            <div class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show mt-4" role="alert">
                <i class="ri-alert-line label-icon blink"></i> Você não possui autorização para prosseguir com a tarefa delegada a outra pessoa
            </div>
        <?php else: ?>
            <?php if($auditorStatus == 'completed'): ?>
                <div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show mt-4" role="alert">
                    <i class="ri-alert-line label-icon blink"></i> Esta Auditoria já foi finalizada e não poderá ser editada
                </div>
            <?php endif; ?>
            <?php if($auditorStatus == 'losted'): ?>
                <div class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show mt-4" role="alert">
                    <i class="ri-alert-line label-icon blink"></i> Esta Auditoria foi perdida pois o prazo expirou e por isso não poderá mais ser editada
                </div>
            <?php endif; ?>

            <?php echo !empty($description) ? '<div class="blockquote custom-blockquote blockquote-outline blockquote-dark rounded mt-2 mb-2"><p class="text-body mb-2">'.$description.'</p><footer class="blockquote-footer mt-0">'.$getAuthorData['name'].' <cite title="'.$authorRoleName.'">'.$authorRoleName.'</cite></footer></div>' : ''; ?>


            <div id="assignment-container">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="survey_id" value="<?php echo e($surveyId); ?>">
                <input type="hidden" name="company_id" value="<?php echo e($companyId); ?>">
                <?php if($surveyData && $responsesData): ?>
                    <?php $__env->startComponent('surveys.layouts.form-auditor-step-cards'); ?>
                        <?php $__env->slot('data', $stepsWithTopics); ?>
                        <?php $__env->slot('responsesData', $responsesData); ?>
                        <?php $__env->slot('auditorStatus', $auditorStatus); ?>
                        <?php $__env->slot('assignmentId', $assignmentId); ?>
                    <?php echo $__env->renderComponent(); ?>
                <?php else: ?>
                    <div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show" role="alert">
                        <i class="ri-alert-line label-icon"></i> Não há dados para gerar os campos deste formulário de Auditoria
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('build/libs/glightbox/js/glightbox.min.js')); ?>"></script>

    <script>
        var surveysIndexURL = "<?php echo e(route('surveysIndexURL')); ?>";
        var surveysCreateURL = "<?php echo e(route('surveysCreateURL')); ?>";
        var surveysEditURL = "<?php echo e(route('surveysEditURL')); ?>";
        var surveysChangeStatusURL = "<?php echo e(route('surveysChangeStatusURL')); ?>";
        var surveysShowURL = "<?php echo e(route('surveysShowURL')); ?>";
        var surveysStoreOrUpdateURL = "<?php echo e(route('surveysStoreOrUpdateURL')); ?>";
    </script>
    <script src="<?php echo e(URL::asset('build/js/surveys.js')); ?>" type="module"></script>

    <script>
        var profileShowURL = "<?php echo e(route('profileShowURL')); ?>";
        var changeAssignmentAuditorStatusURL = "<?php echo e(route('changeAssignmentAuditorStatusURL')); ?>";
        var responsesAuditorStoreOrUpdateURL = "<?php echo e(route('responsesAusitorStoreOrUpdateURL')); ?>";
    </script>
    <script src="<?php echo e(URL::asset('build/js/surveys-auditor.js')); ?>" type="module"></script>

    <script>
        var uploadPhotoURL = "<?php echo e(route('uploadPhotoURL')); ?>";
        var deletePhotoURL = "<?php echo e(route('deletePhotoURL')); ?>";
        var assetUrl = "<?php echo e(URL::asset('/')); ?>";
    </script>
    <script src="<?php echo e(URL::asset('build/js/surveys-attachments.js')); ?>" type="module"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/surveys/assignment/form-auditor.blade.php ENDPATH**/ ?>