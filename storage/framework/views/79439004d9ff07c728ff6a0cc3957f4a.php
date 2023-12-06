<?php $__env->startSection('title'); ?>
    <?php echo e($user->name); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php
        $profileUserId = $user->id;
        $phone = getUserMeta($profileUserId, 'phone');
        $phone = formatPhoneNumber($phone);
        //appPrintR($assignmentData);
        //appPrintR($auditorData);
        //appPrintR($filteredStatuses);
        //appPrintR($assignmentData);
    ?>
    <div class="profile-foreground position-relative mx-n4 mt-n5">
        <div class="profile-wid-bg">
            <img
            <?php if( empty(trim($user->cover))): ?>
                src="<?php echo e(URL::asset('build/images/small/img-9.jpg')); ?>"
            <?php else: ?>
                src="<?php echo e(URL::asset('storage/' . $user->cover)); ?>"
            <?php endif; ?>
            alt="cover" class="profile-wid-img" />
        </div>
    </div>

    <div class="pt-5 mb-2 mb-lg-1 pb-lg-4 profile-wrapper">
        <div class="row g-4">
            <div class="col-auto">
                <div class="avatar-lg profile-user position-relative d-inline-block">
                    <img id="avatar-img"
                    <?php if( empty(trim($user->avatar)) ): ?>
                        src="<?php echo e(URL::asset('build/images/users/user-dummy-img.jpg')); ?>"
                    <?php else: ?>
                        src="<?php echo e(URL::asset('storage/' . $user->avatar)); ?>"
                    <?php endif; ?>
                    alt="avatar" class="img-thumbnail rounded-circle" />
                    <?php if($user->id == auth()->id()): ?>
                        <div class="avatar-xs p-0 rounded-circle profile-photo-edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="right" title="Alterar Avatar">
                            <input class="d-none" name="avatar" id="member-image-input" type="file" accept="image/jpeg">
                            <label for="member-image-input" class="profile-photo-edit avatar-xs">
                                <span class="avatar-title rounded-circle bg-light text-body">
                                    <i class="ri-camera-fill"></i>
                                </span>
                            </label>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col">
                <div class="p-2">
                    <h3 class="text-white mb-1 text-shadow"><?php echo e($user->name); ?></h3>
                    <p class="text-white mb-2 text-shadow"><?php echo e($roleName); ?></p>
                    <div class="hstack text-white gap-1">
                        <div class="me-2 text-shadow">
                            <i class="ri-mail-line text-white fs-16 align-middle me-2"></i><?php echo e($user->email); ?>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-auto order-last order-lg-0">
                <div class="row text text-white-50 text-center">
                    
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header align-items-center d-flex">
            <h5 class="card-title mb-0 flex-grow-1"><i class="ri-calendar-check-fill fs-16 align-bottom text-theme me-2"></i>Tarefas</h5>
        </div>
        <div class="card-body h-100" style="min-height: 150px">
            <?php if( $assignmentData && is_array($assignmentData) ): ?>
                <div class="tasks-board mb-0 position-relative" id="kanbanboard">
                    <?php $__currentLoopData = $filteredStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $filteredSurveyorData = [];
                            $filteredAuditorData = [];

                            array_walk($assignmentData, function ($item) use (&$filteredSurveyorData, $key, $profileUserId) {
                                if ($item['surveyor_status'] == $key && $item['surveyor_id'] == $profileUserId) {
                                    $filteredSurveyorData[] = $item;
                                }
                            });

                            array_walk($assignmentData, function ($item) use (&$filteredAuditorData, $key, $profileUserId) {
                                if ($item['auditor_status'] == $key && $item['auditor_id'] == $profileUserId) {
                                    $filteredAuditorData[] = $item;
                                }
                            });

                            $countFilteredSurveyorData = is_array($filteredSurveyorData) ? count($filteredSurveyorData) : 0;

                            $countFilteredAuditorData = is_array($filteredAuditorData) ? count($filteredAuditorData) : 0;

                            $countTotal = $countFilteredSurveyorData + $countFilteredAuditorData;
                        ?>

                        <div class="tasks-list p-2 <?php echo e(in_array($key, ['waiting', 'auditing', 'pending', 'losted']) && $countTotal < 1 ? 'd-none' : ''); ?>">
                            <div class="d-flex mb-3">
                                <div class="flex-grow-1">
                                    <h6 class="fs-14 text-uppercase fw-semibold mb-0">
                                        <span data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-title="<?php echo e($status['label']); ?>" data-bs-content="<?php echo e($status['description']); ?>">
                                            <?php echo e($status['label']); ?>

                                        </span>
                                        <small class="badge bg-<?php echo e($status['color']); ?> align-bottom ms-1 totaltask-badge">
                                            <?php echo e($countTotal); ?>

                                        </small>
                                    </h6>
                                </div>
                                <div class="flex-shrink-0">
                                    
                                </div>
                            </div>
                            <div data-simplebar class="tasks-wrapper">
                                <div id="<?php echo e($key); ?>-task" class="tasks mb-2">
                                    <?php echo $__env->make('surveys.layouts.profile-task-card', [
                                        'status' => $status,
                                        'statusKey' => $key,
                                        'designated' => 'auditor',
                                        'data' => $filteredAuditorData
                                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                                    <?php echo $__env->make('surveys.layouts.profile-task-card', [
                                        'status' => $status,
                                        'statusKey' => $key,
                                        'designated' => 'surveyor',
                                        'data' => $filteredSurveyorData
                                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                            </div>
                        </div>
                        <!--end tasks-list-->
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    
                </div>
            <?php else: ?>
                <div class="alert alert-info alert-dismissible alert-label-icon label-arrow fade show" role="alert">
                    <i class="ri-alert-line label-icon"></i> Tarefas ainda não lhe foram delegadas
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>

<script>
    var surveysIndexURL = "<?php echo e(route('surveysIndexURL')); ?>";
    var surveysCreateURL = "<?php echo e(route('surveysCreateURL')); ?>";
    var surveysEditURL = "<?php echo e(route('surveysEditURL')); ?>";
    var surveysChangeStatusURL = "<?php echo e(route('surveysChangeStatusURL')); ?>";
    var surveysShowURL = "<?php echo e(route('surveysShowURL')); ?>";
    var surveysStoreOrUpdateURL = "<?php echo e(route('surveysStoreOrUpdateURL')); ?>";
    var formSurveyorAssignmentURL = "<?php echo e(route('formSurveyorAssignmentURL')); ?>";
    var formAuditorAssignmentURL = "<?php echo e(route('formAuditorAssignmentURL')); ?>";
    var changeAssignmentSurveyorStatusURL = "<?php echo e(route('changeAssignmentSurveyorStatusURL')); ?>";
    var changeAssignmentAuditorStatusURL = "<?php echo e(route('changeAssignmentAuditorStatusURL')); ?>";
    var profileShowURL = "<?php echo e(route('profileShowURL')); ?>";
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
    var profileShowURL = "<?php echo e(route('profileShowURL')); ?>";
    var changeAssignmentAuditorStatusURL = "<?php echo e(route('changeAssignmentAuditorStatusURL')); ?>";
    var responsesAuditorStoreOrUpdateURL = "<?php echo e(route('responsesAusitorStoreOrUpdateURL')); ?>";
</script>
<script src="<?php echo e(URL::asset('build/js/surveys-auditor.js')); ?>" type="module"></script>

<script type="module">
    import { attachImage } from '<?php echo e(URL::asset('build/js/settings-attachments.js')); ?>';

    var uploadAvatarURL = "<?php echo e(route('uploadAvatarURL')); ?>";

    attachImage("#member-image-input", "#avatar-img", uploadAvatarURL, false);
</script>

<script>
    // Auto refresh page
    setInterval(function() {
        window.location.reload();// true to cleaning cache
    }, 600000); // 600000 milliseconds = 10 minutes
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/profile/index.blade.php ENDPATH**/ ?>