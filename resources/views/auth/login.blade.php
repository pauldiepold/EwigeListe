@extends('layouts.main')

@section('title', 'Login')

@section('heading', 'Login')

@section('content')
    <div class="row justify-content-center tw-mt-8">
        <div class="col-sm col-md-10 col-lg-8">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group row">
                    <label for="email"
                           class="col-sm-4 col-form-label text-sm-right">E-Mail</label>

                    <div class="col-sm-6">
                        <input id="email" type="email"
                               class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
                               value="{{ old('email') }}" required autofocus>

                        @if ($errors->has('email'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label for="password" class="col-sm-4 col-form-label text-sm-right">Passwort</label>

                    <div class="col-sm-6">
                        <input id="password" type="password"
                               class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                               name="password" required>

                        @if ($errors->has('password'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-6 offset-sm-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember"
                                   id="remember" checked>

                            <label class="form-check-label" for="remember">
                                Angemeldet bleiben
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-6 offset-sm-4">
                        <button type="submit" class="btn btn-primary">
                            Anmelden
                        </button>
                    </div>
                </div>


                <div class="form-group row mb-0">
                    <div class="col-sm-6 offset-sm-4">
                        @if (Route::has('password.request'))
                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                Passwort vergessen?
                            </a>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6 offset-sm-4">
                        <hr>
                        <a href="{{ route('auth.socialite', ['provider' => 'google']) }}"
                           class="btn btn-outline-primary mt-2" style="width: 15rem;">
                            <div class="tw-flex tw-items-center">
                                <i class="fab fa-google tw-text-2xl tw-mr-2 tw-text-gray-700"></i>
                                <span class="tw-flex-1">Mit Google anmelden</span>
                            </div>
                        </a><br>

                        <a href="{{ route('auth.socialite', ['provider' => 'facebook']) }}"
                           class="btn btn-outline-primary my-2" style="width: 15rem;">
                            <div class="tw-flex tw-items-center">
                                <i class="fab fa-facebook tw-text-2xl tw-mr-2" style="color: #3b5998;"></i>
                                <span class="tw-flex-1">Mit Facebook anmelden</span>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="form-group row mb-0">
                    <div class="col-sm-6 offset-sm-4">
                        <hr>
                        Noch kein Konto?<br>
                        <a href="/register" class="btn btn-outline-primary mt-2">
                            Registrieren
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
