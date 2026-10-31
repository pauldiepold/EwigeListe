<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <meta name="theme-color" content="#054b6d" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="default" />
    <meta name="apple-mobile-web-app-title" content="Ewige Liste" />

    <link rel="icon" href="{{ asset('img/favicon_32x32.png') }}" sizes="32x32" />
    <link rel="icon" href="{{ asset('img/favicon_192x192.png') }}" sizes="192x192" />
    <link rel="apple-touch-icon" href="{{ asset('img/apple-touch-icon.png') }}" sizes="180x180" />
    <link rel="manifest" href="{{ asset('manifest.json') }}" />

    @vite(['resources/css/app.css', 'resources/js/inertia/app.ts'])
    @inertiaHead
</head>
<body class="isolate">
@inertia
</body>
</html>
