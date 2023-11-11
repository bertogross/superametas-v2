<!-- ========== App Menu ========== -->
@php
    $getCompanyLogo = getCompanyLogo();
@endphp
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="{{ url('/') }}" class="logo logo-dark" title="Ir para inicial do {{env('APP_NAME')}}">
            <span class="logo-sm">
                <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="{{env('APP_NAME')}}" height="22">
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
        <!-- Light Logo-->
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
        @component('surveys.components.nav')
        @endcomponent
    @endif
    --}}
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
