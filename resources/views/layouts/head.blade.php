<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="theme-color" content="#054b6d">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="#054b6d">
<meta name="apple-mobile-web-app-title" content="Ewige Liste">

<link rel="icon" href="{{ asset('img/favicon_32x32.png') }}" sizes="32x32"/>
<link rel="icon" href="{{ asset('img/favicon_192x192.png') }}" sizes="192x192"/>
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/apple-touch-icon.png') }}">
@stack('scriptsHead')
<link rel="stylesheet" href="{{ mix('/css/app.css') }}">
<link rel="manifest" href="{{ asset('manifest.json') }}">


<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/service-worker.js');
    }
</script>

<title>@yield('title')</title>
