<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.users'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
        <h4 class="mb-sm-0 font-size-18">Equipe</h4>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col">
                            <div class="search-box">
                                <input type="text" class="form-control" id="searchMemberList" placeholder="Pesquisar por nome...">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>
                        <!--end col-->
                        <div class="col-sm-auto ms-auto">
                            <div class="list-grid-nav hstack gap-1">
                                <button type="button" id="grid-view-button" class="btn btn-soft-info nav-link btn-icon fs-14 active filter-button"><i class="ri-grid-fill"></i></button>
                                <button type="button" id="list-view-button" class="btn btn-soft-info nav-link  btn-icon fs-14 filter-button"><i class="ri-list-unordered"></i></button>
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </div>
            </div>
            <div id="teamlist">
                <div class="team-list grid-view-filter row" id="team-member-list">
                    <?php
                    // Sort the users by name in descending order and send with status 0 to the end
                    $users = $users->toArray();

                    usort($users, function ($a, $b) {
                        if ($a['status'] == $b['status']) {
                            return strcmp($a['name'], $b['name']);
                        }
                        return $b['status'] - $a['status'];
                    });

                    $users = collect($users);
                    ?>

                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $id = $user['id'];
                            $capabilities = $user['capabilities'] ? json_decode($user['capabilities'], true) : [];
                            $status = $user['status'];
                            $avatar = $user['avatar'];
                            $cover = $user['cover'];
                            $name = $user['name'];
                            $role = \App\Models\User::getRoleName($user['role']);
                        ?>
                        <?php echo $__env->make('settings.users-card', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
        <div class="col-auto mb-4">
            <div class="card rounded-2 mb-0">
                <div class="card-body p-3">
                    <div class="tasks-wrapper-survey overflow-auto h-100" id="load-surveys-activities" data-subDays="7" style="min-width: 250px;">
                        <div class="text-center">
                            <div class="spinner-border text-theme mt-3 mb-3" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script>
        var uploadAvatarURL = "<?php echo e(route('uploadAvatarURL')); ?>";
        var uploadCoverURL = "<?php echo e(route('uploadCoverURL')); ?>";
        var getUserModalContentURL = "<?php echo e(route('getUserModalContentURL')); ?>";
        var settingsUsersStoreURL = "<?php echo e(route('settingsUsersStoreURL')); ?>";
        var settingsUsersUpdateURL = "<?php echo e(route('settingsUsersUpdateURL')); ?>";
    </script>
    <script src="<?php echo e(URL::asset('build/js/settings-users.js')); ?>" type="module"></script>

    <script>
        var surveysIndexURL = "<?php echo e(route('surveysIndexURL')); ?>";
        var surveysCreateURL = "<?php echo e(route('surveysCreateURL')); ?>";
        var surveysEditURL = "<?php echo e(route('surveysEditURL')); ?>";
        var surveysChangeStatusURL = "<?php echo e(route('surveysChangeStatusURL')); ?>";
        var surveysShowURL = "<?php echo e(route('surveysShowURL')); ?>";
        var surveysStoreOrUpdateURL = "<?php echo e(route('surveysStoreOrUpdateURL')); ?>";
        var getRecentActivitiesURL = "<?php echo e(route('getRecentActivitiesURL')); ?>";
    </script>
    <script src="<?php echo e(URL::asset('build/js/surveys.js')); ?>" type="module"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/team/index.blade.php ENDPATH**/ ?>