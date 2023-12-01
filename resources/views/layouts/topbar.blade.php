@php
    use App\Models\User;

    $getUserData = getUserData();
    $getCompanyLogo = getCompanyLogo();
    $getCompanyName = getCompanyName();
@endphp
<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a href="{{ url('/') }}" class="logo logo-dark" title="Ir para inicial do {{env('APP_NAME')}}">
                        <span class="logo-sm">
                            <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="{{env('APP_NAME')}}" height="22" class="logo-image">
                        </span>
                        <span class="logo-lg">
                            <img
                            @if ($getCompanyLogo)
                                src="{{$getCompanyLogo}}"
                            @else
                                src="{{URL::asset('build/images/logo-dark.png')}}"
                            @endif
                            alt="{{env('APP_NAME')}}" height="39">
                        </span>
                    </a>

                    <a href="{{ url('/') }}" class="logo logo-light" title="Ir para inicial do {{env('APP_NAME')}}">
                        <span class="logo-sm">
                            <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="{{env('APP_NAME')}}" height="22">
                        </span>
                        <span class="logo-lg">
                            <img
                            @if ($getCompanyLogo)
                                src="{{$getCompanyLogo}}"
                            @else
                                src="{{URL::asset('build/images/logo-light.png')}}"
                            @endif
                            alt="{{env('APP_NAME')}}" height="39">
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
                        <!--
                        <div class="p-3 border-top-0 border-start-0 border-end-0 border-dashed border">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 fw-semibold fs-15"> Web Apps </h6>
                                </div>
                                <div class="col-auto">
                                    <a href="#!" class="btn btn-sm btn-soft-info"> View All Apps
                                        <i class="ri-arrow-right-s-line align-middle"></i></a>
                                </div>
                            </div>
                        </div>
                        -->

                        <div class="p-2">
                            <div class="row g-0">
                                <div class="col">
                                    <a class="dropdown-icon-item" href="{{ route('goalSalesIndexURL') }}" title="Meta de Vendas">
                                        <i class="ri-user-smile-line text-theme fs-1"></i>
                                        {{--
                                        <img src="{{ URL::asset('build/images/svg/happy.png') }}" alt="Meta de Vendas">
                                        --}}
                                        <span>Vendas</span>
                                    </a>
                                </div>

                                <!--
                                <div class="col">
                                    <a class="dropdown-icon-item" href="{{-- route('goalResultsIndexURL') --}}#" title="Meta de Resultados">
                                        <img src="{{ URL::asset('build/images/bg-d.png') }}" alt="Meta de Resultados">
                                        <span>Resultados</span>
                                    </a>
                                </div>
                                -->

                                @if(auth()->user()->hasRole(User::ROLE_ADMIN) || auth()->user()->hasRole(User::ROLE_CONTROLLERSHIP))
                                    <div class="col">
                                        <a class="dropdown-icon-item" href="{{ route('surveysIndexURL') }}" title="Vistorias">
                                            <i class="ri-survey-line text-theme fs-1"></i>
                                            {{--
                                            <img src="{{ URL::asset('build/images/verification-img.png') }}" alt="Vistorias">
                                            --}}
                                            <span>Vistorias</span>
                                        </a>
                                    </div>
                                @endif

                                <div class="col">
                                    <a class="dropdown-icon-item" href="{{ route('profileShowURL') }}" title="Tarefas">
                                        <i class="ri-calendar-check-fill text-theme fs-1"></i>
                                        <span>Tarefas</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" data-toggle="fullscreen">
                        <i class="bx bx-fullscreen fs-22"></i>
                    </button>
                </div>

                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button" id="btn-light-dark-mode" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"  data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Alternar Visual">
                        <i class="bx bx-moon fs-22"></i>
                    </button>
                </div>

                @component('components.notifications')
                    @slot('url')
                        {{-- route('notificationsIndexURL') --}}
                    @endslot
                @endcomponent

                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user" src="{{ $getUserData['avatar'] ? $getUserData['avatar'] :  URL::asset('build/images/users/user-dummy-img.jpg') }}" alt="{{$getUserData['name']}}">
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{$getUserData['name']}}</span>
                                <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">
                                    {{ (new User)->getRoleName($getUserData['role']) }}
                                </span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <h6 class="dropdown-header text-uppercase text-center text-theme">{{$getCompanyName}}</h6>
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

                        @if(auth()->user()->hasRole(User::ROLE_ADMIN))
                            <a class="dropdown-item" href="{{ route('settingsUsersIndexURL') }}">
                                <i class="ri-settings-4-fill text-muted fs-16 align-middle me-1"></i>
                                <span class="align-middle">Configurações Gerais</span>
                            </a>
                        @endif

                        <a class="dropdown-item" href="{{ route('profileShowURL') }}">
                            <i class="ri-user-3-fill text-muted fs-16 align-middle me-1"></i>
                            <span class="align-middle">Meu Perfil</span>
                        </a>

                        <!--
                        <a class="dropdown-item" href="auth-lockscreen-basic"><i class="mdi mdi-lock text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Lock screen</span></a>
                        -->

                        <div class="dropdown-divider"></div>

                        <a class="dropdown-item " href="javascript:void();" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="bx bx-power-off font-size-16 align-middle me-1"></i> <span key="t-logout">Sair</span></a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
