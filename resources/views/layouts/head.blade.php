<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" href="{{ asset('img/favicon_32x32.png') }}" sizes="32x32" />
<link rel="icon" href="{{ asset('img/favicon_192x192.png') }}" sizes="192x192" />

<link rel="stylesheet" href="{{ mix('/css/app.css') }}?v={{ time() }}">
<script src="{{ mix('/js/app.js') }}?v={{ time() }}"></script>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css"
      integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">

<title>@yield('title')</title>