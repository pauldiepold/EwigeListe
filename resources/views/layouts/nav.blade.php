<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="row w-100 justify-content-center mx-auto">
        <div class="col text-center text-light">
            <h3>Ewige Liste</h3>
        </div>
        <div class="w-100"></div>
        <div class="col">
            <button class="navbar-toggler mx-auto" type="button" data-toggle="collapse"
                    data-target="#navbarCollapse"
                    aria-controls="navbarsExample08" aria-expanded="false" aria-label="Toggle navigation">
                <i id="fa-bars" class="fas fa-bars"></i>
                <i id="fa-times" class="fas fa-times" style="display:none;"></i>
            </button>

            <div class="collapse navbar-collapse justify-content-md-center" id="navbarCollapse">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/players">Ewige Liste</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/rounds/create">Neue Runde starten</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/rounds">Rundenarchiv</a>
                    </li>

                    <li class="nav-item">
                        @guest
                            <a class="nav-link" href="/login">Login</a>
                        @endguest
                        @auth
                            <a class="nav-link" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                  style="display: none;">
                                @csrf
                            </form>
                        @endauth
                    </li>

                </ul>
            </div>
        </div>
    </div>


</nav>