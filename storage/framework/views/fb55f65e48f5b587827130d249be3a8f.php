<?php
    $currentUserId = auth()->id();

    $surveyId = $surveyData->id ?? '';
    $templateName = $surveyData ? getTemplateNameById($surveyData->template_id) : '';

    $assignmentId = $assignmentData->id ?? null;
    $surveyorStatus = $assignmentData->surveyor_status ?? null;

    $companyId = $assignmentData->company_id ?? '';
    $companyName = $companyId ? getCompanyNameById($companyId) : '';

    $auditorId = $assignmentData->auditor_id ?? null;
    $auditorName = getUserData($auditorId)['name'];

    use Carbon\Carbon;
    use App\Models\SurveyResponse;

    $today = Carbon::today();
    $responsesData = SurveyResponse::where('survey_id', $surveyId)
        ->where('surveyor_id', $currentUserId)
        ->where('company_id', $companyId)
        ->whereDate('created_at', '=', $today)
        ->get()
        ->toArray();
?>

<?php $__env->startSection('title'); ?>
    Formulário de Vistoria
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
            Tarefa <small><i class="ri-arrow-drop-right-fill text-theme ms-2 me-2 align-bottom"></i> #<span class="text-theme"><?php echo e($surveyId); ?></span> <?php echo e(limitChars($templateName ?? '', 20)); ?></small>
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>
    <div id="content" class="rounded rounded-2 mb-4">
        <div class="bg-info-subtle position-relative">
            <div class="card-body p-5 text-center">
                <?php if($companyName ): ?>
                    <h2 class="text-theme text-uppercase"><?php echo e($companyName); ?></h2>
                <?php endif; ?>
                <h2>Vistoria</h2>
                <p>Auditoria será realizada por <u><?php echo e($auditorName); ?></u></p>
                <h3><?php echo e($templateName); ?></h3>
                <div class="mb-0 text-muted">
                    Executar em:
                    
                    <?php echo e($surveyData->created_at ? \Carbon\Carbon::parse($surveyData->created_at)->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY') : '-'); ?>

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

        <?php if($currentUserId != $assignmentData->surveyor_id): ?>
            <div class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show mt-4" role="alert">
                <i class="ri-alert-line label-icon"></i> Você não possui autorização para prosseguir com a tarefa delegada a outra pessoa
            </div>
        <?php else: ?>
            <?php if($surveyorStatus == 'auditing'): ?>
                <div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show mt-4" role="alert">
                    <i class="ri-alert-line label-icon"></i> Esta Vistoria já foi enviada para Auditoria e não poderá ser editada.
                </div>
            <?php endif; ?>
            <?php if($surveyorStatus == 'losted'): ?>
                <div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show mt-4" role="alert">
                    <i class="ri-alert-line label-icon"></i> O prazo expirou e esta Vistoria foi perdida. Por isso não poderá mais ser editada.
                </div>
            <?php endif; ?>
            <div id="assignment-container">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="survey_id" value="<?php echo e($surveyId); ?>">
                <input type="hidden" name="company_id" value="<?php echo e($companyId); ?>">

                <?php if($surveyData): ?>
                    <?php $__env->startComponent('surveys.layouts.form-surveyor-step-cards'); ?>
                        <?php $__env->slot('data', $stepsWithTopics); ?>
                        <?php $__env->slot('responsesData', $responsesData); ?>
                        <?php $__env->slot('purpose', 'validForm'); ?>
                        <?php $__env->slot('surveyorStatus', $surveyorStatus); ?>
                        <?php $__env->slot('assignmentId', $assignmentId); ?>
                    <?php echo $__env->renderComponent(); ?>
                <?php else: ?>
                    <div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show" role="alert">
                        <i class="ri-alert-line label-icon"></i> Não há dados para gerar os campos deste formulário de Vistoria
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
        var formSurveyorAssignmentURL = "<?php echo e(route('formSurveyorAssignmentURL')); ?>";
        var changeAssignmentSurveyorStatusURL = "<?php echo e(route('changeAssignmentSurveyorStatusURL')); ?>";
        var responsesSurveyorStoreOrUpdateURL = "<?php echo e(route('responsesSurveyorStoreOrUpdateURL')); ?>";
        var profileShowURL = "<?php echo e(route('profileShowURL')); ?>";
    </script>
    <script src="<?php echo e(URL::asset('build/js/surveys-surveyor.js')); ?>" type="module"></script>

    <script>
        var uploadPhotoURL = "<?php echo e(route('uploadPhotoURL')); ?>";
        var deletePhotoURL = "<?php echo e(route('deletePhotoURL')); ?>";
    </script>
    <script src="<?php echo e(URL::asset('build/js/surveys-attachments.js')); ?>" type="module"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/surveys/assignment/form-surveyor.blade.php ENDPATH**/ ?>