@php
    $host = $_SERVER['HTTP_HOST'] ?? 'default';
    $logo2 = str_contains($host, 'testing') ? '-2' : '';
@endphp
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

<link rel="icon" type="image/png" href="{{ URL::asset('build/images/logo-sm' . $logo2 . '.png') }}">
<link rel="shortcut icon" href="{{ URL::asset('build/images/favicons/favicon' . $logo2 . '.ico') }}">
