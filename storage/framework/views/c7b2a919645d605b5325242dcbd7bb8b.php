<div id="scrollbar">
    <div class="container-fluid">

        <div id="two-column-menu">
        </div>
        <ul class="navbar-nav" id="navbar-nav">
            <li class="menu-title"><span><?php echo app('translator')->get('translation.menu'); ?></span></li>
            <li class="nav-item">
                <a class="nav-link menu-link" href="#sidebarDashboards" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                    <i class="ri-bar-chart-2-fill"></i> <span><?php echo app('translator')->get('translation.dashboards'); ?></span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarDashboards">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a href="<?php echo e(route('goalSalesIndexURL')); ?>" class="nav-link"><?php echo app('translator')->get('translation.goal-sales'); ?></a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('surveysIndexURL')); ?>" class="nav-link"><?php echo app('translator')->get('translation.surveys'); ?></a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="menu-title"><i class="ri-more-fill"></i> <span><?php echo app('translator')->get('translation.components'); ?></span></li>

            <li class="nav-item">
                <a class="nav-link menu-link <?php echo e(request()->is('settings/account') ? 'active' : ''); ?>" href="<?php echo e(route('settingsAccountShowURL')); ?>">
                    <i class="ri-arrow-right-up-line"></i> <span>Meu <?php echo e(env('APP_NAME')); ?></span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-link <?php echo e(request()->is('settings/api-keys') || request()->is('settings/database') ? 'active' : ''); ?>" href="#sidebarAPIs" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarAPIs">
                    <i class="ri-cloud-windy-fill"></i> <span><?php echo app('translator')->get('translation.api-conections'); ?></span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarAPIs">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a href="<?php echo e(route('settingsApiKeysURL')); ?>" class="nav-link <?php echo e(request()->is('settings/api-keys') ? 'active' : ''); ?>"><?php echo app('translator')->get('translation.api-keys'); ?></a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('settingsDatabaseIndexURL')); ?>" class="nav-link <?php echo e(request()->is('settings/database') ? 'active' : ''); ?>"><?php echo app('translator')->get('translation.your-erp'); ?></a>
                        </li>
                    </ul>
                </div>
            </li>

            <?php if( getDropboxToken() ): ?>
                <li class="nav-item">
                    <a class="nav-link menu-link <?php echo e(request()->is('settings/dropbox') ? 'active' : ''); ?>" href="<?php echo e(route('DropboxIndexURL')); ?>">
                        <i class="ri-dropbox-fill <?php echo e(request()->is('settings/dropbox') ? 'text-primary' : ''); ?>"></i> <span class="<?php echo e(request()->is('settings/dropbox') ? 'text-white' : ''); ?>">Dropbox</span>
                    </a>
                </li>
            <?php endif; ?>

            <li class="nav-item">
                <a class="nav-link menu-link <?php echo e(request()->is('settings/users') ? 'active' : ''); ?>" href="<?php echo e(route('settingsUsersIndexURL')); ?>">
                    <i class="ri-admin-fill"></i> <span><?php echo app('translator')->get('translation.users'); ?></span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-link <?php echo e(request()->is('settings/security') ? 'active' : ''); ?>" href="#">
                    <i class="ri-shield-keyhole-line"></i> <span><?php echo app('translator')->get('translation.security'); ?></span>
                </a>
            </li>

        </ul>
    </div>
</div>
<div class="sidebar-background d-none"></div>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/layouts/nav-settings.blade.php ENDPATH**/ ?>