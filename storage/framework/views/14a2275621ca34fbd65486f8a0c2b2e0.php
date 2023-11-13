<?php
use App\Models\User;

$getActiveCompanies = getActiveCompanies();
//appPrintR($getActiveCompanies);

$getAuthorizedCompanies = $user ? getAuthorizedCompanies($user->id) : $getActiveCompanies;
//appPrintR($getAuthorizedCompanies);

if (is_object($getActiveCompanies)) {
    $extractCompanyIds = $getActiveCompanies->pluck('company_id')->map(function ($value) {
        return (int) $value;
    })->all();
    //appPrintR($extractCompanyIds);
}
?>
<!-- Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">

            <div class="modal-body">
                <form autocomplete="off" id="userForm" class="needs-validation" autocomplete="off" data-id="<?php echo e($user ? $user->id : ''); ?>" novalidate>
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="hidden" name="user_id" value="<?php echo e($user ? $user->id : ''); ?>" class="form-control">

                            <?php if(isset($user)): ?>
                                <!-- Save data in 'users' table collumn 'cover' -->
                                <div class="px-1 pt-1">
                                    <div class="modal-team-cover position-relative mb-0 mt-n4 mx-n4 rounded-top overflow-hidden" style="min-height: 140px;">
                                        <img
                                        <?php if( empty(trim($user->cover)) ): ?>
                                            src="<?php echo e(URL::asset('build/images/small/img-9.jpg')); ?>"
                                        <?php else: ?>
                                            src="<?php echo e(URL::asset('storage/' . $user->cover)); ?>"
                                        <?php endif; ?>
                                        alt="cover" id="cover-img" class="img-fluid" data-user-id="<?php echo e($user ? $user->id : ''); ?>">

                                        <div class="d-flex position-absolute start-0 end-0 top-0 p-3">
                                            <div class="flex-grow-1">
                                                <h5 class="modal-title text-white" id="modalUserTitle"></h5>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <div class="d-flex gap-3 align-items-center">
                                                    <div>
                                                        <label for="cover-image-input" class="mb-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Select Cover Image">
                                                            <div class="avatar-xs">
                                                                <div class="avatar-title bg-light border rounded-circle text-muted cursor-pointer">
                                                                    <i class="ri-image-fill"></i>
                                                                </div>
                                                            </div>
                                                        </label>
                                                        <input class="form-control d-none" name="cover" id="cover-image-input" type="file" accept="image/png, image/gif, image/jpeg">
                                                    </div>
                                                    <button type="button" class="btn-close btn-close-white" id="createMemberBtn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Save data in 'users' table collumn 'avatar' -->
                                <div class="text-center mb-4 mt-n5 pt-2">
                                    <div class="position-relative d-inline-block">
                                        <div class="position-absolute bottom-0 end-0">
                                            <label for="member-image-input" class="mb-0" data-bs-toggle="tooltip" data-bs-placement="right" title="Select Member Image">
                                                <div class="avatar-xs">
                                                    <div class="avatar-title bg-light border rounded-circle text-muted cursor-pointer">
                                                        <i class="ri-image-fill"></i>
                                                    </div>
                                                </div>
                                            </label>
                                            <input class="form-control d-none" name="avatar" id="member-image-input" type="file" accept="image/jpeg">
                                        </div>
                                        <div class="avatar-lg">
                                            <div class="avatar-title bg-light rounded-circle">
                                                <img
                                                <?php if( empty(trim($user->avatar)) ): ?>
                                                    src="<?php echo e(URL::asset('build/images/users/user-dummy-img.jpg')); ?>"
                                                <?php else: ?>
                                                    src="<?php echo e(URL::asset('storage/' . $user->avatar)); ?>"
                                                <?php endif; ?>
                                                id="avatar-img" alt="avatar" class="avatar-md rounded-circle h-auto" data-user-id="<?php echo e($user ? $user->id : ''); ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <button type="button" class="btn-close btn-close-white float-end" id="createMemberBtn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                                <h5 class="modal-title text-white mb-3" id="modalUserTitle"></h5>
                            <?php endif; ?>

                            <!-- Save data in 'users' table collumn 'name' -->
                            <div class="form-group mb-3">
                                <label for="teammembersName" class="form-label"> Nome Completo </label>
                                <input type="text" class="form-control" id="teammembersName" name="name" placeholder="Informe o nome" value="<?php echo e($user ? $user->name : ''); ?>" maxlength="100" required>
                            </div>

                            <!-- Save data in 'users' table collumn 'email' -->
                            <div class="form-group mb-4">
                                <label for="teammembersEmail" class="form-label">Endereço de E-mail </label>
                                <input type="email" class="form-control" id="teammembersEmail" name="email" placeholder="Informe o endereço de e-mail corporativo" value="<?php echo e($user ? $user->email : ''); ?>" required>
                            </div>

                            <?php if(isset($user)): ?>
                                <!-- Save data in 'users' table collumn 'password' -->
                                <div class="form-group mb-4">
                                    <label class="form-label">Nova Senha <i class="ri-question-line text-primary non-printable align-top" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-title="Alterar Senha" data-bs-content="A nova senha deve conter entre 8 e 15 caracteres.<br>Componha utilizando números + letras maiúsculas + minúsculas."></i></label>
                                    <div class="position-relative auth-pass-inputgroup">
                                        <input type="password" name="new_password" id="password-input" minlength="8" maxlength="20" class="form-control password-input" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">
                                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle text-body"></i></button>
                                    </div>
                                    <div class="form-text">
                                        A senha deve ser composta por entre 8 e 20 caracteres.<br>
                                        <span class="text-warning">Para não modificar a senha, deixe este campo vazio.</span>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if( !isset($user) || $user->role !=1 ): ?>
                                <!-- Save data in 'users' table collumn 'role'-->
                                <div class="form-group mb-4">
                                    <label class="form-label">Nível <i class="ri-question-line text-primary non-printable align-top" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-title="Níveis e Permissões" data-bs-content="<ul class='list-unstyled mb-0'><li>Saiba mais visualizando ao final desta página a tabela contendo o grid de Níveis e Permissões</li></ul>"></i></label>
                                    <select class="form-control form-select" name="role">
                                        <option class="text-body" disabled selected>- Selecione -</option>
                                        <?php $__currentLoopData = User::CAPABILITIES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $roleId => $capabilities): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($roleId != 1): ?>
                                                <option class="text-muted" <?php if(isset($user) && $roleId == $user->role): ?> selected <?php endif; ?> value="<?php echo e($roleId); ?>"><?php echo e((new User)->getRoleName($roleId)); ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            <?php endif; ?>

                            <!-- Save data in 'users' table collumn 'status' -->
                            <div class="form-group mb-5">
                                <div class="form-check form-switch form-switch-theme form-switch-lg">
                                    <input type="checkbox" class="form-check-input" name="status" id="user_status_1"
                                    <?php if($user): ?>
                                        <?php if(1 == $user->status): ?>
                                            checked
                                        <?php endif; ?>
                                    <?php else: ?>
                                        checked
                                    <?php endif; ?>
                                    value="1">
                                    <label class="form-check-label" for="user_status_1" data-bs-html="true" data-bs-toggle="tooltip" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="Ativo = Verde">Status <i class="ri-question-line text-primary non-printable align-top" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="Quando Desativado este usuário não terá sucesso ao tentar efetuar login em seu <?php echo e(env('APP_NAME')); ?>"></i></label>
                                </div>
                                
                            </div>

                            <!-- Save data in 'user_metas' table collumn 'meta_key' and 'meta_value'-->
                            <?php if(isset($user) && $user->id == 1): ?>
                                <?php if(isset($extractCompanyIds)): ?>
                                    <input type="hidden" name="companies" value="<?php echo e(json_encode($extractCompanyIds)); ?>">
                                <?php else: ?>
                                    <div class="alert alert-warning">Empresas ainda não foram cadastradas/ativadas</div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="mb-3">
                                    <label class="form-label">Empresas Autorizadas <i class="ri-question-line text-primary non-printable align-top" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="Selecione as empresas em que este usuário poderá obter acesso aos dados"></i></label>
                                    <?php if(isset($getActiveCompanies) && count($getActiveCompanies) > 0): ?>
                                        <div class="row">
                                            <?php $__currentLoopData = $getActiveCompanies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="col-md-6">
                                                    <div class="form-check form-switch form-switch-theme form-switch-md">
                                                        <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        role="switch"
                                                        <?php if($user): ?>
                                                            <?php echo e(!empty($getAuthorizedCompanies) && is_array($getAuthorizedCompanies) && in_array(intval($company->company_id), $getAuthorizedCompanies) ? 'checked' : ''); ?>

                                                        <?php else: ?>
                                                            checked
                                                        <?php endif; ?>
                                                        id="company-<?php echo e($company->company_id); ?>"
                                                        name="companies[]"
                                                        value="<?php echo e($company->company_id); ?>">
                                                        <label class="form-check-label" for="company-<?php echo e($company->company_id); ?>"><?php echo e(empty($company->company_alias) ? e($company->company_name) : e($company->company_alias)); ?></label>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-warning">Empresas ainda não foram cadastradas/ativadas</div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <div class="hstack gap-2 justify-content-end">
                                <button type="submit" class="btn btn-theme" id="btn-save-user"></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!--end modal-content-->
    </div>
    <!--end modal-dialog-->
</div>
<!--end modal-->
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views\settings\users-form.blade.php ENDPATH**/ ?>