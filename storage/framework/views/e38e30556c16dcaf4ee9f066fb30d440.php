<div id="scrollbar">
    <div class="container-fluid">

        <div id="two-column-menu">
        </div>
        <ul class="navbar-nav" id="navbar-nav">
            

            <li class="menu-title"><i class="ri-more-fill"></i> <span><?php echo app('translator')->get('translation.components'); ?></span></li>

            <li class="nav-item">
                <a href="<?php echo e(route('surveysIndexURL')); ?>" class="nav-link menu-link
                <?php echo e(request()->is('surveys/listing') ||
                request()->is('surveys/listing-cards') ||
                request()->is('surveys/create')
                ? 'active' : ''); ?>

                " title="Listar Vistorias"><i class="ri-todo-line"></i> Vistorias</a>
            </li>
            <li class="nav-item">
                <a href="<?php echo e(route('surveysComposeIndexURL')); ?>" class="nav-link menu-link
                <?php echo e(request()->is('surveys/compose/listing') ||
                request()->is('surveys/compose/create/default') ||
                request()->is('surveys/compose/create/custom')
                ? 'active' : ''); ?>

                " title="Listar Formulários"><i class="ri-list-check-2"></i> Formulários</a>
            </li>

        </ul>
    </div>
</div>
<div class="sidebar-background d-none"></div>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views\surveys\components\_deprecated\nav.blade.php ENDPATH**/ ?>