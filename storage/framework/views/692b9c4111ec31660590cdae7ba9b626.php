<?php
    use App\Models\User;
    use App\Models\SurveyAssignments;

    $user = auth()->user();
    $currentUserCapabilities = $user->capabilities ? json_decode($user->capabilities, true) : [];

    $getUserData = getUserData();
    $getCompanyLogo = getCompanyLogo();
    $getCompanyName = getCompanyName();

    $host = $_SERVER['HTTP_HOST'] ?? 'default';
    $logo2 = str_contains($host, 'testing') ? '-2' : '';
?>
<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a href="<?php echo e(url('/')); ?>" class="logo logo-dark" title="Ir para inicial do <?php echo e(appName()); ?>">
                        <span class="logo-sm">
                            <img src="<?php echo e(URL::asset('build/images/logo-sm' . $logo2 . '.png')); ?>" alt="<?php echo e(appName()); ?>" height="31" loading="lazy">
                        </span>
                        <span class="logo-lg">
                            <img
                            <?php if($getCompanyLogo): ?>
                                src="<?php echo e($getCompanyLogo); ?>"
                            <?php else: ?>
                                src="<?php echo e(URL::asset('build/images/logo-dark' . $logo2 . '.png')); ?>"
                            <?php endif; ?>
                            alt="<?php echo e(appName()); ?>" height="31" loading="lazy">
                        </span>
                    </a>

                    <a href="<?php echo e(url('/')); ?>" class="logo logo-light" title="Ir para inicial do <?php echo e(appName()); ?>">
                        <span class="logo-sm">
                            <img src="<?php echo e(URL::asset('build/images/logo-sm' . $logo2 . '.png')); ?>" alt="<?php echo e(appName()); ?>" height="31" loading="lazy">
                        </span>
                        <span class="logo-lg">
                            <img
                            <?php if($getCompanyLogo): ?>
                                src="<?php echo e($getCompanyLogo); ?>"
                            <?php else: ?>
                                src="<?php echo e(URL::asset('build/images/logo-light' . $logo2 . '.png')); ?>"
                            <?php endif; ?>
                            alt="<?php echo e(appName()); ?>" height="31" loading="lazy">
                        </span>
                    </a>
                </div>

                <!--
                <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger" id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
                -->
            </div>

            <div class="d-flex align-items-center">

                <!--
                <div class="dropdown d-md-none topbar-head-dropdown header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bx bx-search fs-22"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-search-dropdown">
                        <form class="p-3">
                            <div class="form-group m-0">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                                    <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                -->

                <div class="dropdown topbar-head-dropdown ms-1 header-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Módulos">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class='bx bx-category-alt fs-22'></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg p-0 dropdown-menu-end">
                        

                        <div class="p-2">
                            <div class="row g-0">
                                <?php if(getERP()): ?>
                                    <div class="col">
                                        <a class="dropdown-icon-item" href="<?php echo e(route('goalSalesIndexURL')); ?>" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="bottom" title="Meta de Vendas">
                                            <i class="ri-shopping-cart-2-fill text-theme fs-1"></i>
                                            
                                            <span>Vendas</span>
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <!--
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#" title="Meta de Resultados">
                                        <img src="<?php echo e(URL::asset('build/images/bg-d.png')); ?>" alt="Meta de Resultados" loading="lazy">
                                        <span>Resultados</span>
                                    </a>
                                </div>
                                -->

                                <?php if(auth()->user()->hasRole(User::ROLE_ADMIN) || auth()->user()->hasRole(User::ROLE_CONTROLLERSHIP)): ?>
                                    <div class="col">
                                        <a class="dropdown-icon-item" href="<?php echo e(route('surveysIndexURL')); ?>" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="bottom"  title="Acessar a sessão Checklists">
                                            <i class="ri-checkbox-line text-theme fs-1"></i>
                                            
                                            <span>Checklists</span>
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="col">
                                        <a class="dropdown-icon-item" href="<?php echo e(route('profileShowURL')); ?>" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="bottom" title="Acessar minha lista de Tarefas">

                                            <span class="position-absolute ms-4 mt-1 translate-middle badge rounded-pill bg-warning" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Tarefas por executar"><?php echo e(SurveyAssignments::countSurveyAssignmentSurveyorTasks($user->id, ['new', 'pending', 'in_progress'])); ?><span class="visually-hidden">tasks</span></span>

                                            <i class="ri-todo-fill text-theme fs-1"></i>

                                            <span>Tarefas</span>
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <div class="col">
                                    <a class="dropdown-icon-item" href="<?php echo e(route('teamIndexURL')); ?>" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="bottom" title="Listar membros da Equipe">
                                        <i class="ri-team-fill text-theme fs-1"></i>
                                        <span>Equipe</span>
                                    </a>
                                </div>
                            </div>

                            
                        </div>
                    </div>
                </div>

                

                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button" id="btn-light-dark-mode" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"  data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="bottom" title="Alternar Visual">
                        <i class="bx bx-moon fs-22"></i>
                    </button>
                </div>

                <?php $__env->startComponent('components.notifications'); ?>
                    <?php $__env->slot('url'); ?>
                        
                    <?php $__env->endSlot(); ?>
                <?php echo $__env->renderComponent(); ?>

                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user" src="<?php echo e($getUserData['avatar'] ? $getUserData['avatar'] :  URL::asset('build/images/users/user-dummy-img.jpg')); ?>" alt="<?php echo e($getUserData['name']); ?>" loading="lazy">
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text"><?php echo e($getUserData['name']); ?></span>
                                <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">
                                    <?php echo e(\App\Models\User::getRoleName($getUserData['role'])); ?>

                                </span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <h6 class="dropdown-header text-uppercase text-center text-theme"><?php echo e($getCompanyName); ?></h6>
                        <!--
                        <h6 class="dropdown-header">Welcome Anna!</h6>
                        -->
                        <div class="dropdown-divider"></div>


                        <!--
                        <a class="dropdown-item" href="pages-profile"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Profile</span></a>

                        <a class="dropdown-item" href="apps-chat"><i class="mdi mdi-message-text-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Messages</span></a>

                        <a class="dropdown-item" href="apps-tasks-kanban"><i class="mdi mdi-calendar-check-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Taskboard</span></a>

                        <a class="dropdown-item" href="pages-faqs"><i class="mdi mdi-lifebuoy text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Help</span></a>
                        <div class="dropdown-divider"></div>
                        -->

                        <?php if(auth()->user()->hasRole(User::ROLE_ADMIN)): ?>
                            <a class="dropdown-item" href="<?php echo e(route('settingsUsersIndexURL')); ?>">
                                <i class="ri-settings-4-fill text-muted fs-16 align-middle me-1"></i>
                                <span class="align-middle">Configurações Gerais</span>
                            </a>
                        <?php endif; ?>

                        

                        <a class="dropdown-item" href="<?php echo e(route('profileShowURL')); ?>">
                            <i class="ri-todo-fill text-muted fs-16 align-middle me-1"></i>
                            <span class="align-middle">
                                Minhas Tarefas
                                <span class="position-relative ms-3 mt-2 translate-middle badge rounded-pill bg-warning" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Tarefas por executar"><?php echo e(SurveyAssignments::countSurveyAssignmentSurveyorTasks($user->id, ['new', 'pending', 'in_progress'])); ?><span class="visually-hidden">tasks</span></span>
                        </span>
                        </a>

                        <?php if(in_array('audit', $currentUserCapabilities)): ?>
                            <a class="dropdown-item" href="<?php echo e(route('surveysAuditIndexURL', $user->id)); ?>">
                                <i class="ri-fingerprint-2-line text-muted fs-16 align-middle me-1"></i>
                                <span class="align-middle">
                                    Minhas Auditorias
                                    <span class="position-relative ms-3 mt-2 translate-middle badge rounded-pill bg-secondary" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Auditorias por executar"><?php echo e(SurveyAssignments::countSurveyAssignmentAuditorTasks($user->id, ['new', 'pending', 'in_progress'])); ?><span class="visually-hidden">tasks</span></span>
                                </span>
                            </a>
                        <?php endif; ?>

                        <!--
                        <a class="dropdown-item" href="auth-lockscreen-basic"><i class="mdi mdi-lock text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Lock screen</span></a>
                        -->

                        <div class="dropdown-divider"></div>

                        <a class="dropdown-item " href="javascript:void();" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="bx bx-power-off font-size-16 align-middle me-1"></i> <span key="t-logout">Sair</span></a>
                        <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                            <?php echo csrf_field(); ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/layouts/topbar.blade.php ENDPATH**/ ?>