<!doctype html >
@php
    $userTheme = getUserMeta(auth()->id(), 'theme');
    $host = $_SERVER['HTTP_HOST'] ?? 'default';
    $logo2 = str_contains($host, 'testing') ? '-2' : '';
@endphp
<html class="no-focus" moznomarginboxes mozdisallowselectionprint lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="horizontal" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="enable" data-bs-theme="{{ $userTheme ?? 'dark' }}" data-layout-width="fluid" data-layout-position="fixed" data-layout-style="default" data-sidebar-visibility="show"><head>
<head>
    <meta charset="utf-8" />
    <title>@yield('title') | {{appName()}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="cache-control" content="no-cache">
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="expires" content="-1">
    <meta name="robots" content="noindex,nofollow,nopreview,nosnippet,notranslate,noimageindex,nomediaindex,novideoindex,noodp,noydir">
    <meta property="og:image" content="{{ URL::asset('build/images/logo-sm' . $logo2 . '.png') }}">
    <meta content="Solução para ajudar sua equipe a atingir e Superar suas Metas de Vendas" name="description" />
    <meta name="author" content="{{appName()}}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->
    <link rel="icon" type="image/png" href="{{ URL::asset('build/images/logo-sm' . $logo2 . '.png') }}">
    <link rel="shortcut icon" href="{{ URL::asset('build/images/favicons/favicon' . $logo2 . '.ico')}}">
    @include('layouts.head-css')
</head>

    @section('body')
        @include('layouts.body')
    @show

        <!--preloader-->
        <div id="preloader">
            <div id="status">
                <div class="spinner-border text-theme avatar-sm" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>

        <!-- Begin page -->
        <div id="layout-wrapper">
            @include('layouts.topbar')
            @include('layouts.sidebar')
            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">
                <div class="page-content">
                    <div class="container-fluid">
                        @yield('content')
                    </div>
                    <!-- container-fluid -->
                </div>
                <!-- End Page-content -->
                @include('layouts.footer')
            </div>
            <!-- end main content-->
        </div>
        <!-- END layout-wrapper -->

        <!-- JAVASCRIPT -->
        @include('layouts.vendor-scripts')

        <div id="custom-backdrop" class="d-none text-white">
            <div style="display: flex; align-items: flex-end; justify-content: flex-start; height: 100vh; padding: 25px; padding-bottom: 70px;">
                Para continuar trabalhando enquanto este processo está em andamento, <a href="{{ url('/') }}" target="_blank" class="text-theme me-1 ms-1">clique aqui</a> para abrir o {{ appName() }} em nova guia
            </div>
        </div>

        <div id="modalContainer"></div>

    </body>
</html>
