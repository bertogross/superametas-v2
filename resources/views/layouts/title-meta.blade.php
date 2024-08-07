@php
    $host = $_SERVER['HTTP_HOST'] ?? 'default';
    $logo2 = str_contains($host, 'testing') ? '-2' : '';
@endphp
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
<link rel="icon" type="image/png" href="{{ URL::asset('build/images/logo-sm' . $logo2 . '.png') }}">
<link rel="shortcut icon" href="{{ URL::asset('build/images/favicons/favicon' . $logo2 . '.ico') }}">
<link rel="manifest" href="{{ URL::asset('build/json/manifest.json') }}">
