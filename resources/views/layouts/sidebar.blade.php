<!-- ========== App Menu ========== -->
@php
    $getCompanyLogo = getCompanyLogo();

    $host = $_SERVER['HTTP_HOST'] ?? 'default';
    $logo2 = str_contains($host, 'testing') ? '-2' : '';
@endphp
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="{{ url('/') }}" class="logo logo-dark" title="Ir para inicial do {{appName()}}">
            <span class="logo-sm">
                <img src="{{ URL::asset('build/images/logo-sm' . $logo2 . '.png') }}" alt="{{appName()}}" height="22">
            </span>
            <span class="logo-lg">
                <img
                @if ($getCompanyLogo)
                    src="{{$getCompanyLogo}}"
                @else
                    src="{{URL::asset('build/images/logo-dark' . $logo2 . '.png')}}"
                @endif
                alt="{{appName()}}" height="39">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="{{ url('/') }}" class="logo logo-light" title="Ir para inicial do {{appName()}}">
            <span class="logo-sm">
                <img src="{{ URL::asset('build/images/logo-sm' . $logo2 . '.png') }}" alt="{{appName()}}" height="22">
            </span>
            <span class="logo-lg">
                <img
                @if ($getCompanyLogo)
                    src="{{$getCompanyLogo}}"
                @else
                    src="{{URL::asset('build/images/logo-light' . $logo2 . '.png')}}"
                @endif
                alt="{{appName()}}" height="39">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <!-- Conditional Navigation Include -->
    @if ( Request::is('settings*') )
        @component('settings.components.nav')
        @endcomponent
    @endif
    {{--
    @if ( Request::is('surveys*') )
        @component('surveys.layouts.nav')
        @endcomponent
    @endif
    --}}
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
