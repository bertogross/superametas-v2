@extends('layouts.master')
@section('title')
    @lang('translation.users')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('url')
            {{ url('settings') }}
        @endslot
        @slot('li_1')
            @lang('translation.settings')
        @endslot
        @slot('title')
            @lang('translation.users')
        @endslot
    @endcomponent

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

                        <button class="btn btn-success addMembers-modal" data-bs-toggle="modal" data-bs-target="#addmemberModal"><i class="ri-add-fill me-1 align-bottom"></i> Adicionar</button>
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
                        @foreach ($users as $user)
                            @component('components.settings-card-user')
                                @slot('id') {{ $user->id }} @endslot
                                @slot('title') {{ $user->name }} @endslot
                                @slot('role') {{ $user->getRoleName($user->role) }} @endslot
                            @endcomponent
                        @endforeach
                    </div>
                </div>
                <div class="py-4 mt-4 text-center" id="noresult" style="display: none;">
                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:72px;height:72px"></lord-icon>
                    <h5 class="mt-4">Nenhum resultado encontrado</h5>
                </div>

                <div class="offcanvas offcanvas-end border-0" tabindex="-1" id="member-overview">
                    <!--end offcanvas-header-->
                    <div class="offcanvas-body profile-offcanvas p-0">
                        <div class="team-cover">
                            <img src="{{URL::asset('build/images/small/img-9.jpg')}}" alt="" class="img-fluid" />
                        </div>
                        <div class="p-3">
                            <div class="team-settings">
                                <div class="row">
                                    <div class="col">
                                        <button type="button" class="btn btn-light btn-icon rounded-circle btn-sm favourite-btn "> <i class="ri-star-fill fs-14"></i> </button>
                                    </div>
                                    <div class="col text-end dropdown">
                                        <a href="javascript:void(0);" id="dropdownMenuLink14" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ri-more-fill fs-17"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink14">
                                            <li><a class="dropdown-item" href="javascript:void(0);"><i class="ri-star-line me-2 align-middle"></i>Favorites</a></li>
                                            <li><a class="dropdown-item" href="javascript:void(0);"><i class="ri-delete-bin-5-line me-2 align-middle"></i>Delete</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!--end col-->
                        </div>
                        <div class="p-3 text-center">
                            <img src="{{URL::asset('build/images/users/avatar-2.jpg')}}" alt="" class="avatar-lg img-thumbnail rounded-circle mx-auto profile-img">
                            <div class="mt-3">
                                <h5 class="fs-15 profile-name">Nancy Martino</h5>
                                <p class="text-muted profile-designation">Team Leader & HR</p>
                            </div>
                            <div class="hstack gap-2 justify-content-center mt-4">
                                <div class="avatar-xs">
                                    <a href="javascript:void(0);" class="avatar-title bg-secondary-subtle text-secondary rounded fs-16">
                                        <i class="ri-facebook-fill"></i>
                                    </a>
                                </div>
                                <div class="avatar-xs">
                                    <a href="javascript:void(0);" class="avatar-title bg-success-subtle text-success rounded fs-16">
                                        <i class="ri-slack-fill"></i>
                                    </a>
                                </div>
                                <div class="avatar-xs">
                                    <a href="javascript:void(0);" class="avatar-title bg-info-subtle text-info rounded fs-16">
                                        <i class="ri-linkedin-fill"></i>
                                    </a>
                                </div>
                                <div class="avatar-xs">
                                    <a href="javascript:void(0);" class="avatar-title bg-danger-subtle text-danger rounded fs-16">
                                        <i class="ri-dribbble-fill"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="row g-0 text-center">
                            <div class="col-6">
                                <div class="p-3 border border-dashed border-start-0">
                                    <h5 class="mb-1 profile-project">124</h5>
                                    <p class="text-muted mb-0">Projects</p>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-6">
                                <div class="p-3 border border-dashed border-start-0">
                                    <h5 class="mb-1 profile-task">81</h5>
                                    <p class="text-muted mb-0">Tasks</p>
                                </div>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                        <div class="p-3">
                            <h5 class="fs-15 mb-3">Personal Details</h5>
                            <div class="mb-3">
                                <p class="text-muted text-uppercase fw-semibold fs-12 mb-2">Number</p>
                                <h6>+(256) 2451 8974</h6>
                            </div>
                            <div class="mb-3">
                                <p class="text-muted text-uppercase fw-semibold fs-12 mb-2">Email</p>
                                <h6>nancymartino@email.com</h6>
                            </div>
                            <div>
                                <p class="text-muted text-uppercase fw-semibold fs-12 mb-2">Location</p>
                                <h6 class="mb-0">Carson City - USA</h6>
                            </div>
                        </div>
                        <div class="p-3 border-top">
                            <h5 class="fs-15 mb-4">File Manager</h5>
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0 avatar-xs">
                                    <div class="avatar-title bg-danger-subtle text-danger rounded fs-16">
                                        <i class="ri-image-2-line"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1"><a href="javascript:void(0);">Images</a></h6>
                                    <p class="text-muted mb-0">4469 Files</p>
                                </div>
                                <div class="text-muted">
                                    12 GB
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0 avatar-xs">
                                    <div class="avatar-title bg-secondary-subtle text-secondary rounded fs-16">
                                        <i class="ri-file-zip-line"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1"><a href="javascript:void(0);">Documents</a></h6>
                                    <p class="text-muted mb-0">46 Files</p>
                                </div>
                                <div class="text-muted">
                                    3.46 GB
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0 avatar-xs">
                                    <div class="avatar-title bg-success-subtle text-success rounded fs-16">
                                        <i class="ri-live-line"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1"><a href="javascript:void(0);">Media</a></h6>
                                    <p class="text-muted mb-0">124 Files</p>
                                </div>
                                <div class="text-muted">
                                    4.3 GB
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="flex-shrink-0 avatar-xs">
                                    <div class="avatar-title bg-primary-subtle text-primary rounded fs-16">
                                        <i class="ri-error-warning-line"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1"><a href="javascript:void(0);">Others</a></h6>
                                    <p class="text-muted mb-0">18 Files</p>
                                </div>
                                <div class="text-muted">
                                    846 MB
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end offcanvas-body-->
                    <div class="offcanvas-foorter border p-3 hstack gap-3 text-center position-relative">
                        <button class="btn btn-light w-100"><i class="ri-question-answer-fill align-bottom ms-1"></i> Send Message</button>
                        <a href="profile" class="btn btn-primary w-100"><i class="ri-user-3-fill align-bottom ms-1"></i> View Profile</a>
                    </div>
                </div>
                <!--end offcanvas-->
            </div>
        </div><!-- end col -->
    </div>
    <!--end row-->

    <!-- Include User Modal from resources/views/components/settings-modal-user-form.blade -->
    @include('components/settings-modal-user-form')


@endsection
@section('script')
    <script src="{{ URL::asset('build/js/settings-users.js') }}"></script>

    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
