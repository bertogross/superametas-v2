<div class="col" data-search-user-id="{{ $id }}" data-search-user-name="@if(isset($name)) {{ $name }} @endif" data-search-user-role="@if(isset($role)){{ $role }}@endif">
    <div class="card team-box">
        <div class="team-cover" style="min-height: 140px">
            <img
            @if( empty(trim($cover)) )
                src="{{ URL::asset('build/images/small/img-9.jpg') }}"
            @else
                src="{{ URL::asset('storage/' . $cover) }}"
            @endif
            alt="@if(isset($name)){{ $name }}@endif" class="img-fluid" height="140" id="cover-img-@if(isset($id)){{ $id }}@endif">
        </div>
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
                                <li><a class="dropdown-item btn-edit-user cursor-pointer" data-user-id="{{ $id }}" data-user-name="@if(isset($name)) {{ $name }} @endif"><i class="ri-pencil-line me-2 align-bottom text-muted"></i>Editar</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col">
                    <div class="team-profile-img">
                        <div class="avatar-lg img-thumbnail rounded-circle flex-shrink-0"><img
                            @if( empty(trim($avatar)) )
                                src="{{ URL::asset('build/images/users/user-dummy-img.jpg') }}"
                            @else
                                src="{{ URL::asset('storage/' . $avatar) }}"
                            @endif
                            alt="@if(isset($name)){{ $name }}@endif"
                            class="member-img img-fluid d-block rounded-circle" id="avatar-img-@if(isset($id)){{ $id }}@endif">
                        </div>
                        <div class="team-content">
                            <h5 class="fs-16 mb-1">
                                @if(isset($name))
                                    {{ $name }}
                                @endif
                            </h5>
                            <p class="text-muted member-designation mb-0">
                                @if(isset($role))
                                    {{ $role }}
                                @endif
                            </p>
                            <p class="text-muted member-designation mb-0">
                                @if(isset($status) && $status == '1')
                                    <span class="text-theme">Ativo</span>
                                @else
                                    <span class="text-danger">Inoperante</span>
                                @endif
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
                    <div class="text-end"> <a href="{{ route('profileShowURL') }}@if(isset($id))/{{ $id }}@endif" class="btn btn-light view-btn">Visualizar Perfil</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
