<div id="scrollbar">
    <div class="container-fluid">

        <div id="two-column-menu">
        </div>
        <ul class="navbar-nav" id="navbar-nav">
            

            <li class="menu-title"><i class="ri-more-fill"></i> <span><?php echo app('translator')->get('translation.components'); ?></span></li>

            <li class="nav-item">
                <a class="nav-link menu-link <?php echo e(request()->is('settings/account') ? 'active' : ''); ?>" href="<?php echo e(route('settingsAccountShowURL')); ?>">
                    <i class="ri-arrow-right-up-line"></i> <span>Meu <?php echo e(env('APP_NAME')); ?></span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-link <?php echo e(request()->is('settings/users') ? 'active' : ''); ?>" href="<?php echo e(route('settingsUsersIndexURL')); ?>">
                    <i class="ri-admin-fill"></i> <span><?php echo app('translator')->get('translation.users'); ?></span>
                </a>
            </li>

            <?php if(getERP()): ?>
                <li class="nav-item">
                    <a class="nav-link menu-link <?php echo e(request()->is('settings/database') ? 'active' : ''); ?>" href="<?php echo e(route('settingsDatabaseIndexURL')); ?>">
                        <i class="ri-database-2-line"></i> <?php echo app('translator')->get('translation.your-erp'); ?>
                    </a>
                </li>
            <?php endif; ?>

            <li class="nav-item">
                <a class="nav-link menu-link <?php echo e(request()->is('settings/api-keys') ? 'active' : ''); ?>" href="<?php echo e(route('settingsApiKeysURL')); ?>">
                    <i class="ri-cloud-windy-fill"></i> <?php echo app('translator')->get('translation.api-conections'); ?>
                </a>
            </li>

            

            <?php if( getDropboxToken() ): ?>
                <li class="nav-item">
                    <a class="nav-link menu-link <?php echo e(request()->is('settings/dropbox') ? 'active' : ''); ?>" href="<?php echo e(route('DropboxIndexURL')); ?>">
                        <i class="ri-dropbox-fill <?php echo e(request()->is('settings/dropbox') ? 'text-primary' : ''); ?>"></i> <span class="<?php echo e(request()->is('settings/dropbox') ? 'text-white' : ''); ?>">Armazenamento</span>
                    </a>
                </li>
            <?php endif; ?>

            <li class="nav-item">
                <a class="nav-link menu-link <?php echo e(request()->is('settings/security') ? 'active' : ''); ?>" href="#">
                    <i class="ri-shield-keyhole-line"></i> <span><?php echo app('translator')->get('translation.security'); ?></span>
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="sidebar-background d-none"></div>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/settings/components/nav.blade.php ENDPATH**/ ?>