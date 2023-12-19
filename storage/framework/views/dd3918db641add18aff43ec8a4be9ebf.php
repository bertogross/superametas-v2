<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.users'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('url'); ?>
            <?php echo e(url('settings')); ?>

        <?php $__env->endSlot(); ?>
        <?php $__env->slot('li_1'); ?>
            <?php echo app('translator')->get('translation.settings'); ?>
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            <?php echo app('translator')->get('translation.users'); ?>
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="card">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-sm-4">
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

                        <button id="btn-add-user" class="btn btn-theme"><i class="ri-add-fill me-1 align-bottom"></i> Adicionar</button>
                    </div>
                </div>
                <!--end col-->
            </div>
            <!--end row-->
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div>
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

                <hr class="w-50 start-50 position-relative translate-middle-x clearfix mt-4 mb-5">

                <?php echo \App\Models\User::generatePermissionsTable(); ?>


            </div>
        </div><!-- end col -->
    </div>
    <!--end row-->

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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/settings/users.blade.php ENDPATH**/ ?>