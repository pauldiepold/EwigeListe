<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" href="{{ asset('img/favicon_32x32.png') }}" sizes="32x32"/>
<link rel="icon" href="{{ asset('img/favicon_192x192.png') }}" sizes="192x192"/>

<link rel="stylesheet" href="{{ mix('/css/app.css') }}">
<style>
	   .card-header {
		   border: 0;
	   }
	.card-footer {
		border: 0;
	}
	.card {
		    box-shadow: 0 4px 6px 0 hsla(0, 0%, 0%, 0.2);
		border: 0px;
	}
	.h4, h4 {
		font-size: 1.38rem;	
	}
</style>

@stack('scriptsHead')
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