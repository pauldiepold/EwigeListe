<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="theme-color" content="#054b6d">
<link rel="icon" href="{{ asset('img/favicon_32x32.png') }}" sizes="32x32"/>
<link rel="icon" href="{{ asset('img/favicon_192x192.png') }}" sizes="192x192"/>

@stack('scriptsHead')

<link rel="stylesheet" href="{{ mix('/css/app.css') }}">

<title>@yield('title')</title>
@if(Auth::id() != 1)
    <script type="text/javascript">
        var _paq = window._paq || [];
        _paq.push(['setCustomDimension', 1, @auth'{{ Auth::user()->player->surname }} {{ Auth::user()->player->name }}'@endauth @guest'Anonym'@endguest]);
        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);
        (function () {
            var u = "https://matomo.pauldiepold.de/";
            _paq.push(['setTrackerUrl', u + 'matomo.php']);
            _paq.push(['setSiteId', '3']);
            var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
            g.type = 'text/javascript';
            g.async = true;
            g.defer = true;
            g.src = u + 'matomo.js';
            s.parentNode.insertBefore(g, s);
        })();
    </script>
@endif
