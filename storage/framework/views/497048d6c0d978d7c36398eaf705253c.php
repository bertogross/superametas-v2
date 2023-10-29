<div class="col" data-search-user-id="<?php echo e($id); ?>" data-search-user-name="<?php if(isset($name)): ?> <?php echo e($name); ?> <?php endif; ?>" data-search-user-role="<?php if(isset($role)): ?><?php echo e($role); ?><?php endif; ?>">
    <div class="card team-box">
        <div class="team-cover"> <img
            <?php if(empty(trim($cover))): ?>
                src="<?php echo e(URL::asset('build/images/small/img-9.jpg')); ?>"
            <?php else: ?>
                src="<?php echo e(URL::asset('storage/' . $cover)); ?>"
            <?php endif; ?>
            alt="<?php if(isset($name)): ?> <?php echo e($name); ?> <?php endif; ?>" class="img-fluid" id="cover-img-<?php if(isset($id)): ?><?php echo e($id); ?><?php endif; ?>"> </div>
        <div class="card-body p-4">
            <div class="row align-items-center team-row">
                <div class="col team-settings">
                    <div class="row">
                        <div class="col">
                            <div class="flex-shrink-0 me-2">
                                <!--
                                <button type="button" class="btn btn-light btn-icon rounded-circle btn-sm favourite-btn "> <i class="ri-star-fill fs-14"></i> </button>
                                -->
                           </div>
                        </div>
                        <div class="col text-end dropdown">
                            <a href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false"> <i class="ri-more-fill fs-17 text-theme"></i> </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item btn-edit-user cursor-pointer" data-user-id="<?php echo e($id); ?>" data-user-name="<?php if(isset($name)): ?> <?php echo e($name); ?> <?php endif; ?>"><i class="ri-pencil-line me-2 align-bottom text-muted"></i>Editar</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col">
                    <div class="team-profile-img">
                        <div class="avatar-lg img-thumbnail rounded-circle flex-shrink-0"><img
                            <?php if(empty(trim($avatar))): ?>
                                src="<?php echo e(URL::asset('build/images/users/user-dummy-img.jpg')); ?>"
                            <?php else: ?>
                            src="<?php echo e(URL::asset('storage/' . $avatar)); ?>"
                            <?php endif; ?>
                            alt="<?php if(isset($name)): ?><?php echo e($name); ?><?php endif; ?>"
                            class="member-img img-fluid d-block rounded-circle" id="avatar-img-<?php if(isset($id)): ?><?php echo e($id); ?><?php endif; ?>">
                        </div>
                        <div class="team-content">
                            <h5 class="fs-16 mb-1">
                                <?php if(isset($name)): ?>
                                    <?php echo e($name); ?>

                                <?php endif; ?>
                            </h5>
                            <p class="text-muted member-designation mb-0">
                                <?php if(isset($role)): ?>
                                    <?php echo e($role); ?>

                                <?php endif; ?>
                            </p>
                            <p class="text-muted member-designation mb-0">
                                <?php if(isset($status) && $status == '1'): ?>
                                    <span class="text-theme">Ativo</span>
                                <?php else: ?>
                                    <span class="text-danger">Inoperante</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col">
                    <div class="row text-muted text-center">
                        <div class="col-6 border-end border-end-dashed">
                            <h5 class="mb-1 projects-num">225</h5>
                            <p class="text-muted mb-0">Projects</p>
                        </div>
                        <div class="col-6">
                            <h5 class="mb-1 tasks-num">197</h5>
                            <p class="text-muted mb-0">Tasks</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col">
                    <div class="text-end"> <a href="<?php echo e(route('profileShowURL')); ?><?php if(isset($id)): ?>/<?php echo e($id); ?><?php endif; ?>" class="btn btn-light view-btn">Visualizar Perfil</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views/settings/users-card.blade.php ENDPATH**/ ?>