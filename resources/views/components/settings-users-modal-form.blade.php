@php
use App\Models\User;

$selectedCompanies = !empty($selectedCompanies) ? json_decode($selectedCompanies , true) : [];

@endphp
<!-- Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">

            <div class="modal-body">
                <form autocomplete="off" id="memberlist-form" class="needs-validation" autocomplete="off" data-id="{{ $user ? $user->id : '' }}" novalidate>
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="hidden" name="user_id" value="{{ $user ? $user->id : '' }}" class="form-control" value="">

                            @if(isset($user))
                                <!-- Save data in 'users' table collumn 'cover' -->
                                <div class="px-1 pt-1">
                                    <div class="modal-team-cover position-relative mb-0 mt-n4 mx-n4 rounded-top overflow-hidden">
                                        <img
                                        @if(empty(trim($user->cover)))
                                            src="{{URL::asset('build/images/small/img-9.jpg')}}"
                                        @else
                                            src="{{ URL::asset('storage/' . $user->cover) }}"
                                        @endif
                                        alt="cover" id="cover-img" class="img-fluid"  data-user-id="{{ $user ? $user->id : '' }}">

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
                                                        <input class="form-control d-none" name="cover" value="" id="cover-image-input" type="file" accept="image/png, image/gif, image/jpeg">
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
                                            <input class="form-control d-none" name="avatar" value="" id="member-image-input" type="file" accept="image/jpeg">
                                        </div>
                                        <div class="avatar-lg">
                                            <div class="avatar-title bg-light rounded-circle">
                                                <img
                                                @if(empty(trim($user->avatar)))
                                                    src="{{URL::asset('build/images/users/user-dummy-img.jpg')}}"
                                                @else
                                                    src="{{ URL::asset('storage/' . $user->avatar) }}"
                                                @endif
                                                id="avatar-img" alt="avatar" class="avatar-md rounded-circle h-auto" data-user-id="{{ $user ? $user->id : '' }}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <button type="button" class="btn-close btn-close-white float-end" id="createMemberBtn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                                <h5 class="modal-title text-white mb-3" id="modalUserTitle"></h5>
                            @endif

                            <!-- Save data in 'users' table collumn 'name' -->
                            <div class="form-group mb-3">
                                <label for="teammembersName" class="form-label"> Nome Completo </label>
                                <input type="text" class="form-control" id="teammembersName" name="name" placeholder="Informe o nome" value="{{ $user ? $user->name : '' }}" maxlength="100" required>
                            </div>

                            <!-- Save data in 'users' table collumn 'email' -->
                            <div class="form-group mb-3">
                                <label for="teammembersEmail" class="form-label">Endereço de E-mail </label>
                                <input type="email" class="form-control" id="teammembersEmail" name="email" placeholder="Informe o endereço de e-mail corporativo" value="{{ $user ? $user->email : '' }}" required>
                            </div>

                            @if(isset($user))
                                <!-- Save data in 'users' table collumn 'password' -->
                                <div class="form-group mb-4">
                                    <label class="form-label">Nova Senha <i class="ri-question-line text-primary non-printable align-top" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-title="Alterar Senha" data-bs-content="A nova senha deve conter entre 8 e 15 caracteres.<br>Componha utilizando números + letras maiúsculas + minúsculas."></i></label>
                                    <div class="position-relative auth-pass-inputgroup">
                                        <input type="password" name="new_password" id="password-input" maxlength="8" class="form-control password-input" autocomplete="false" readonly onfocus="this.removeAttribute('readonly');">
                                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle text-body"></i></button>
                                    </div>
                                    <div class="small text-muted">Para não modificar a senha, deixe este campo vazio.</div>
                                </div>
                            @endif

                            @if( !isset($user) || $user->id != 1)
                                <!-- Save data in 'users' table collumn 'role'-->
                                <div class="form-group mb-3">
                                    <label class="form-label">Nível <i class="ri-question-line text-primary non-printable align-top" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-title="Níveis e Permissões" data-bs-content="<ul class='list-unstyled mb-0'><li>Saiba mais na tabela Níveis e Permissões</li></ul>"></i></label>
                                    <select class="form-control form-select" name="role">
                                        <option class="text-body" value="" disabled selected>- Selecione -</option>
                                        @foreach(User::CAPABILITIES as $roleId => $capabilities)
                                            @if($roleId != 1)
                                                <option class="text-muted" @if(isset($user) && $roleId == $user->role) selected @endif value="{{ $roleId }}">{{ (new User)->getRoleName($roleId) }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            @if(isset($user) && $user->id != 1)
                                <!-- Save data in 'users' table collumn 'status' -->
                                <div class="form-group mb-4 ">
                                    <label class="form-label">Status <i class="ri-question-line text-primary non-printable align-top" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="Quando Inoperante, o usuário não poderá mais efetuar login em seu Supera Metas"></i></label>
                                    <div class="row">
                                      <div class="col">
                                            <div class="form-check form-switch form-switch-theme">
                                                <input type="radio" class="form-check-input" name="status" id="user_status_1" @if(1 == $user->status) checked @endif value="1">
                                                <label class="form-check-label" for="user_status_1">Ativo</label>
                                            </div>
                                      </div>
                                      <div class="col">
                                            <div class="form-check form-switch form-switch-danger">
                                                <input type="radio" class="form-check-input" name="status" id="user_status_0" @if(0 == $user->status) checked @endif value="0">
                                                <label class="form-check-label" for="user_status_0">Inoperante</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Save data in 'user_metas' table collumn 'meta_key' and 'meta_value'-->
                            @if(isset($user) && $user->id == 1)
                                @if(isset($companies) && count($companies) > 0)
                                    @php
                                        $companiesArray = $companies->pluck('company_id')->map(function($value) {
                                            return (int) $value;
                                        })->toArray();

                                        $encodedValue = json_encode($companiesArray);
                                    @endphp
                                    <input type="hidden" name="companies" value="{{ $encodedValue }}">
                                @else
                                    <p>Empresas ainda não foram cadastradas</p>
                                @endif
                            @else
                                <div class="mb-4">
                                    <label class="form-label">Empresas Autorizadas</label>
                                    @if(isset($companies) && count($companies) > 0)
                                        <div class="row">
                                            @foreach($companies as $company)
                                                <div class="col-md-6">
                                                    <div class="form-check form-switch form-switch-theme form-switch-md">
                                                        <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        role="switch"
                                                        {{ in_array($company->company_id, $selectedCompanies) ? 'checked' : '' }}
                                                        id="company-{{ $company->company_id }}"
                                                        name="companies[]"
                                                        value="{{ $company->company_id }}">
                                                        <label class="form-check-label" for="company-{{ $company->company_id }}">{{ empty($company->company_alias) ? e($company->company_name) : e($company->company_alias) }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="form-text">Selecione as empresas em que este usuário poderá obter acesso aos dados.</div>
                                    @else
                                        <p>Empresas ainda não foram cadastradas</p>
                                    @endif
                                </div>
                            @endif

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
