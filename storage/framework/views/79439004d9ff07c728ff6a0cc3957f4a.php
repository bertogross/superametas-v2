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
    ?>
    <div class="profile-foreground position-relative mx-n5 mt-n4">
        <div class="profile-wid-bg mx-n5">
            <img
            <?php if( empty(trim($user->cover)) || !file_exists(URL::asset('storage/' . $user->cover)) ): ?>
                src="<?php echo e(URL::asset('build/images/small/img-9.jpg')); ?>"
            <?php else: ?>
                src="<?php echo e(URL::asset('storage/' . $user->cover)); ?>"
            <?php endif; ?>
            alt="cover" class="profile-wid-img" />
        </div>
    </div>

    <div class="pt-4 mb-2 mb-lg-1 pb-lg-4 profile-wrapper">
        <div class="row g-4">
            <div class="col-auto">
                <div class="avatar-lg">
                    <img
                    <?php if( empty(trim($user->avatar)) ): ?>
                        src="<?php echo e(URL::asset('build/images/users/user-dummy-img.jpg')); ?>"
                    <?php else: ?>
                        src="<?php echo e(URL::asset('storage/' . $user->avatar)); ?>"
                    <?php endif; ?>
                    alt="avatar" class="img-thumbnail rounded-circle" />
                </div>
            </div>
            <div class="col">
                <div class="p-2">
                    <h3 class="text-white mb-1"><?php echo e($user->name); ?></h3>
                    <p class="text-white text-opacity-75 mb-2"><?php echo e($roleName); ?></p>
                    <div class="hstack text-white-50 gap-1">
                        <div class="me-2">
                            <i class="ri-mail-line text-white text-opacity-75 fs-16 align-middle me-2"></i><?php echo e($user->email); ?>

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
            <h5 class="card-title mb-0 flex-grow-1"><i class="ri-survey-line fs-16 align-bottom text-theme me-2"></i>Tarefas</h5>
        </div>
        <div class="card-body">
            <div class="tasks-board mb-0 position-relative" id="kanbanboard">
                <?php $__currentLoopData = $filteredStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $filteredSurveyorData = [];
                        $filteredAuditorData = [];

                        if($assignmentData){
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
                        }

                        $countFilteredSurveyorData = is_array($filteredSurveyorData) ? count($filteredSurveyorData) : 0;

                        $countFilteredAuditorData = is_array($filteredAuditorData) ? count($filteredAuditorData) : 0;
                    ?>
                    <div class="tasks-list p-2">
                        <div class="d-flex mb-3">
                            <div class="flex-grow-1">
                                <h6 class="fs-14 text-uppercase fw-semibold mb-0">
                                    <span data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-title="<?php echo e($status['label']); ?>" data-bs-content="<?php echo e($status['description']); ?>">
                                        <?php echo e($status['label']); ?>

                                    </span>
                                    <small class="badge bg-<?php echo e($status['color']); ?> align-bottom ms-1 totaltask-badge">
                                        <?php echo e($countFilteredSurveyorData + $countFilteredAuditorData); ?>

                                    </small>
                                </h6>
                            </div>
                            <div class="flex-shrink-0">
                                
                            </div>
                        </div>
                        <div data-simplebar class="tasks-wrapper" style="max-height: 100vh;">
                            <div id="<?php echo e($key); ?>-task" class="tasks mb-2">
                                <?php if( $assignmentData && is_array($assignmentData) ): ?>
                                    <?php echo $__env->make('surveys.layouts.profile-task-card', [
                                        'status' => $status,
                                        'statusKey' => $key,
                                        'designated' => 'auditor',
                                        'data' => $filteredAuditorData
                                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <?php endif; ?>

                                <?php if( $assignmentData && is_array($assignmentData) ): ?>
                                    <?php echo $__env->make('surveys.layouts.profile-task-card', [
                                        'status' => $status,
                                        'statusKey' => $key,
                                        'designated' => 'surveyor',
                                        'data' => $filteredSurveyorData
                                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <!--end tasks-list-->
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </div>
            <!--end task-board-->
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/profile/index.blade.php ENDPATH**/ ?>