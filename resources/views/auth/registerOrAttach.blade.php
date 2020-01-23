@extends('layouts.main')

@section('title', ucfirst($socialiteUser->provider) . ' Login')

@section('heading', ucfirst($socialiteUser->provider) . ' Login')

@push('scriptsHead')
    <script type="text/javascript">
        if (window.location.hash && window.location.hash === '#_=_') {
            window.location.hash = '';
        }
    </script>
@endpush

@section('content')

    <toggle provider="{{ ucfirst($socialiteUser->provider) }}">

        <template v-slot:content1>
            @empty($user)
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <input type="hidden" name="email" value="{{ $socialiteUser->email }}">
                    <input type="hidden" name="socialiteUserId" value="{{ $socialiteUser->id }}">

                    <div class="tw-mb-6 tw-max-w-2xs tw-mx-auto">

                        <label for="surname" class="">Vorname</label>
                        <input id="surname" type="text"
                               class="form-control {{ $errors->has('surname') ? ' is-invalid' : '' }}" name="surname"
                               value="{{ explode(' ', $socialiteUser->name, 2)[0] }}" required>

                        @if ($errors->has('surname'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('surname') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="tw-mb-6 tw-max-w-2xs tw-mx-auto">

                        <label for="name" class="">Name</label>
                        <input id="name" type="text"
                               class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name"
                               value="@php if (isset($socialiteUser)) { echo explode(' ', $socialiteUser->name, 2)[1]; } @endphp"
                               required>

                        @if ($errors->has('name'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                    </div>


                    <button type="submit" class="btn btn-outline-primary mt-2">
                        <div class="tw-flex tw-items-center">
                            <div
                                style="{{ $socialiteUser->provider == 'facebook' ? 'color: #3b5998;' : 'color: black' }}">
                                <i class="fab fa-{{ $socialiteUser->provider }} tw-text-2xl tw-mr-2"></i>
                            </div>
                            <div>
                                <span>Mit {{ ucfirst($socialiteUser->provider) }} registrieren</span>
                            </div>
                        </div>
                    </button>
                </form>
            @endempty
            @isset($user)
                <p>Es existiert bereits ein Account mit der Mailadresse <b>{{ $socialiteUser->email }}</b>.<br>Klicke
                    "Zurück" und
                    verbinden diesen Account mit Facebook.</p>
            @endisset
        </template>

        <template v-slot:content2>

            <p>
                Gib deine Anmeldedaten ein, um deinen Account mit {{ ucfirst($socialiteUser->provider) }} zu verknüpfen.<br>In
                Zukunft kannst du dich
                mit einem Klick einloggen!
            </p>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <input type="hidden" name="socialiteUserId" value="{{ $socialiteUser->id }}">

                <div class="tw-mb-6 tw-max-w-2xs tw-mx-auto">
                    <label for="email" class="tw-font-bold">E-Mail</label>
                    <input id="email" type="email"
                           class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }} " name="email"
                           value="{{ old('email') }}" required autofocus>

                    @if ($errors->has('email'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="tw-mb-6 tw-max-w-2xs tw-mx-auto">
                    <label for="password" class="tw-font-bold">Passwort</label>
                    <input id="password" type="password"
                           class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                           name="password" required>

                    @if ($errors->has('password'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-check tw-mb-6">
                    <input class="form-check-input" type="checkbox" name="remember"
                           id="remember" checked>

                    <label class="form-check-label" for="remember">
                        Angemeldet bleiben
                    </label>
                </div>

                <button type="submit" class="btn btn-outline-primary tw-mb-6">
                    <div class="tw-flex tw-items-center">
                        <div style="{{ $socialiteUser->provider == 'facebook' ? 'color: #3b5998;' : 'color: black' }}">
                            <i class="fab fa-{{ $socialiteUser->provider }} tw-text-2xl tw-mr-2"></i>
                        </div>
                        <div>
                            <span>Mit {{ ucfirst($socialiteUser->provider) }} verbinden</span>
                        </div>
                    </div>
                </button>

                <div class="form-group  mb-0">
                    @if (Route::has('password.request'))
                        <a class="btn btn-link" href="{{ route('password.request') }}">
                            Passwort vergessen?
                        </a>
                    @endif
                </div>
            </form>
        </template>
    </toggle>
@endsection
