@php
$host = $_SERVER['HTTP_HOST'] ?? 'default';
$logo2 = str_contains($host, 'testing') ? '-2' : '';
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-layout-style="default" data-layout-position="fixed"  data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-layout-width="fluid">
    <head>
        <meta charset="utf-8" />
        <title>@yield('title') | {{appName()}}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="pragma" content="no-cache">
        <meta http-equiv="Expires" content="-1">
        <meta name="robots" content="noindex,nofollow,nopreview,nosnippet,notranslate,noimageindex,nomediaindex,novideoindex,noodp,noydir">
        <meta property="og:image" content="{{ URL::asset('build/images/logo-sm' . $logo2 . '.png') }}">
        <meta content="{{ appDescription() }}" name="description" />
        <meta name="author" content="{{appName()}}" />
        <meta name="theme-color" content="#1a1d21" />
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- App favicon -->
        <link rel="icon" type="image/png" href="{{ URL::asset('build/images/logo-sm' . $logo2 . '.png') }}">
        <link rel="shortcut icon" href="{{ URL::asset('build/images/favicons/favicon' . $logo2 . '.ico')}}">
        <link rel="manifest" href="{{ URL::asset('build/json/manifest.json') }}">
        @include('layouts.head-css')
    </head>
    <body class="{{ str_contains($_SERVER['SERVER_NAME'], 'app.') ? 'production' : 'development' }}">
        <script>0</script>
        <!-- Begin page -->
        <div id="layout-wrapper">
            @include('layouts.topbar')
            @include('layouts.sidebar')
            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">
                <div class="page-content">
                    <!-- Start content -->
                    <div class="container-fluid">
                        @yield('content')
                    </div> <!-- content -->
                </div>
                @include('layouts.footer')
            </div>
            <!-- ============================================================== -->
            <!-- End Right content here -->
            <!-- ============================================================== -->
        </div>
        <!-- END wrapper -->


        @include('layouts.vendor-scripts')

        <div id="custom-backdrop" class="d-none text-white">
            <div style="display: flex; align-items: flex-end; justify-content: flex-start; height: 100vh; padding: 25px; padding-bottom: 70px;">
                Para continuar navegando enquanto este processo está em andamento, <a href="{{ url('/') }}" target="_blank" class="text-theme me-1 ms-1" title="clique aqui">clique aqui</a> para abrir o {{ appName() }} em nova guia
            </div>
        </div>

        <div id="modalContainer"></div>
    </body>
</html>
