<!doctype html>
<html lang="de">
<head>

    <meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="csrf-token" content="XTNkrehWnamEIPt8C01bV3vKQQPPwcoQflf6VzTa">
<link rel="icon" href="https://dev.ewige-liste.de/img/favicon_32x32.png" sizes="32x32"/>
<link rel="icon" href="https://dev.ewige-liste.de/img/favicon_192x192.png" sizes="192x192"/>

<link rel="stylesheet" href="/css/app.css?id=8e08398fd4f8c0218a5a">

<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>-->
<title>Runde Starten</title>
	
</head>

<body class="text-center">

    <div class="py-2" style="background-color: #054b6d;">
    <a href="/" class="link-unstyled">
        <h3 class="site-title text-light mb-0" style="text-shadow: 0em 0.1em 0.17em #444444;">Ewige Liste</h3>
    </a>
</div>
<style>
    #navbar-toggler:active,
    #havbar-toggler:hover {
        background-color: transparent;
    }
</style>
<nav class="navbar navbar-expand-lg navbar-dark bg-secondary sticky-top" id="navbar">
    <button class="navbar-toggler mx-auto" id="navbar-toggler" type="button" data-toggle="collapse"
            data-target="#navbarCollapse"
            aria-controls="navbarsExample08" aria-expanded="false" aria-label="Toggle navigation">
        <i id="fa-bars" class="fas fa-bars"></i>
        <i id="fa-times" class="fas fa-times" style="display:none;"></i>
    </button>

    <div class="collapse navbar-collapse justify-content-md-center text-light" id="navbarCollapse">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="/home">Startseite</a>
            </li>
                            <li class="nav-item">
                    <a class="nav-link" href="/rounds/current">Aktuelle Runde</a>
                </li>
                        <li class="nav-item">
                <a class="nav-link" href="/players">Ewige Liste</a>
            </li>
                                        <li class="nav-item">
                    <a class="nav-link" href="/rounds/create">Neue Runde starten</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/rounds">Rundenarchiv</a>
                </li>
                        <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Sonstiges
                </a>

                <div class="dropdown-menu bg-secondary text-light">
                                            <a href="/invitations" class="dropdown-item">Einladung</a>
                                        <a href="/regeln" class="dropdown-item">Regeln</a>
                    <a href="/impressum" class="dropdown-item">Impressum</a>
                    <a href="/datenschutz" class="dropdown-item">Datenschutz</a>
                </div>
            </li>
        </ul>
    </div>
</nav>

<div id="app">

    <div class="row align-items-center mx-auto my-4 no-gutters" style="max-width:230px">

        <div class="col-2" style="padding:0px;">
                <a @click="numberOfPlayers = numberOfPlayers - 1" v-if="numberOfPlayers > 4">
                    <i class="fas fa-minus-square fa-2x text-primary"></i>
                </a>
        </div>

        <div class="col-8">
				<span class="text-dark" style="font-size:1.15em">
                        <i v-for="index in numberOfPlayers" class="fas fa-user" style="margin: 0 0.1rem;">
					 		
						</i>
				</span>
        </div>

        <div class="col-2" style="padding:0px;">
                <a @click="numberOfPlayers = numberOfPlayers + 1" v-if="numberOfPlayers < 7">
                    <i class="fas fa-plus-square fa-2x text-primary"></i>
                </a>
        </div>
    </div>



    @include('include.error')

    <form method="POST" action="/rounds">
        @csrf

		<input type="hidden" name="numberOfPlayers" v-model="numberOfPlayers">
		
        @for ($k = 0; $k < 7; $k++)
            <div v-if="{{ $k }} < numberOfPlayers" class="form-group mx-auto mt-4" style="max-width:200px;">
                <label for="player{{ $k }}"><b>Spieler {{ $k+1 }}:</b></label>
                <select class="form-control" name="players[{{ $k }}]" v-model="players[{{ $k }}]">
                    @foreach ($players as $player)
                        <option value="{{ $player->id }}" {{ (old("players")[$k] == $player->id || ($loop->index == $k && old("players")[$k] == null) ? "selected" : "") }}>
                            {{--  {{ (old("players")[$k] == $player->id || ($loop->index == $k && old("players")[$k] == null) ? "selected" : "") }} --}}
                            {{ $player->surname }} {{ $player->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endfor

        <button type="submit" class="btn btn-primary mt-3">Neue Runde Starten</button>
    </form>
    <div class="mt-4">
        <a data-container="body" data-toggle="popover" data-placement="top"
           data-content="Die Spieler entsprechend ihrer Sitzreihenfolge auswählen. Spieler 1 beginnt als Geber!">
            <i class="fas fa-info-circle fa-lg"></i>
        </a>
    </div>


</div>


@php
$preselectedPlayers = array();
for($k = 0; $k < 7; $k++) {
	$preselectedPlayers[$k] = $players[$k]->id;
}
@endphp

<script>	

var preselectedPlayers = {{ json_encode($preselectedPlayers) }};
var app2 = new Vue({
	el: '#app',
	data: {
		numberOfPlayers: 4,
		players: preselectedPlayers
	}
})
	
</script>


	
	</body>
</html>