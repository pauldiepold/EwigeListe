<div class="bg-dark py-2">
    <a href="/" class="link-unstyled">
        <h3 class="site-title text-light mb-0">Ewige Liste</h3>
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
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="/rounds/current">Aktuelle Runde</a>
                        </li>
                    @endauth
                    <li class="nav-item">
                        <a class="nav-link" href="/players">Ewige Liste</a>
                    </li>
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="/login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/register">Registrieren</a>
                        </li>
                    @endguest
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="/rounds/create">Neue Runde starten</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/rounds">Rundenarchiv</a>
                        </li>
                    @endauth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Sonstiges
                        </a>

                        <div class="dropdown-menu bg-secondary text-light">
                            @auth
                                <a href="/invitations" class="dropdown-item">Einladung</a>
                            @endauth
                            <a href="/regeln" class="dropdown-item">Regeln</a>
                            <a href="/impressum" class="dropdown-item">Impressum</a>
                            <a href="/datenschutz" class="dropdown-item">Datenschutz</a>
                        </div>
                    </li>
                </ul>
            </div>
		</nav>
	

<!-- <nav class="navbar navbar-expand-lg navbar-dark bg-secondary fixed-top" id="navbar">
    <div class="row w-100 justify-content-center mx-auto">
        <div class="col">
            <a href="/" class="link-unstyled">
                <h3 class="site-title text-light">Ewige Liste</h3>
            </a>
        </div>
        <div class="w-100"></div>
        <div class="col">
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
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="/rounds/current">Aktuelle Runde</a>
                        </li>
                    @endauth
                    <li class="nav-item">
                        <a class="nav-link" href="/players">Ewige Liste</a>
                    </li>
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="/login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/register">Registrieren</a>
                        </li>
                    @endguest
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="/rounds/create">Neue Runde starten</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/rounds">Rundenarchiv</a>
                        </li>
                    @endauth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Sonstiges
                        </a>

                        <div class="dropdown-menu bg-secondary text-light">
                            @auth
                                <a href="/invitations" class="dropdown-item">Einladung</a>
                            @endauth
                            <a href="/regeln" class="dropdown-item">Regeln</a>
                            <a href="/impressum" class="dropdown-item">Impressum</a>
                            <a href="/datenschutz" class="dropdown-item">Datenschutz</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>


</nav>
-->