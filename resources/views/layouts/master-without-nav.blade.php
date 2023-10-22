<!doctype html>
<html class="no-focus" moznomarginboxes mozdisallowselectionprint lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="horizontal" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-layout-mode="dark" data-layout-style="default" data-layout-width="fluid" data-layout-position="fixed" data-preloader="disable" data-bs-theme="dark">
    <head>
        <meta charset="utf-8" />
        <title>@yield('title') | {{env('APP_NAME')}}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Solution to help your team reach and exceed their sales goals" name="description" />
        <meta content="Supera Metas" name="author" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex, nofollow" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ URL::asset('build/images/favicons/favicon.ico')}}">
            @include('layouts.head-css')
    </head>

        @yield('body')

        @yield('content')

        @include('layouts.vendor-scripts')

        <div id="custom-backdrop" class="d-none text-muted">
            <div style="display: flex; align-items: flex-end; justify-content: flex-start; height: 100vh; padding: 25px;">
                Para continuar trabalhando enquanto este processo está em andamento, <a href="{{ url('/') }}" target="_blank" class="text-theme me-1 ms-1">clique aqui</a> para abrir o {{ env('APP_NAME') }} em nova guia
            </div>
        </div>

        <div id="modalContainer"></div>

    </body>
</html>
