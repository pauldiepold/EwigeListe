@extends('layouts.main')

@section('title', 'Registrieren')

@section('heading', 'Registrieren')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm col-md-11 col-lg-8">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="form-group row">
                        <label for="surname"
                               class="col-sm-5 col-form-label text-sm-right font-weight-bold">Vorname:</label>

                        <div class="col-sm-6">
                            <input id="surname" type="text"
                                   class="form-control{{ $errors->has('surname') ? ' is-invalid' : '' }}" name="surname"
                                   value="{{ old('surname') }}" required autofocus>

                            @if ($errors->has('surname'))
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('surname') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="name" class="col-sm-5 col-form-label text-sm-right font-weight-bold">Name:</label>

                        <div class="col-sm-6">
                            <input id="name" type="text"
                                   class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name"
                                   value="{{ old('name') }}" required>

                            @if ($errors->has('name'))
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="email"
                               class="col-sm-5 col-form-label text-sm-right font-weight-bold">E-Mail:</label>

                        <div class="col-sm-6">
                            <input id="email" type="email"
                                   class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
                                   value="{{ old('email') }}" required>

                            @if ($errors->has('email'))
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="password"
                               class="col-sm-5 col-form-label text-sm-right font-weight-bold">Passwort:</label>

                        <div class="col-sm-6">
                            <input id="password" type="password"
                                   class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                   name="password" value="{{ old('password') }}" required>

                            @if ($errors->has('password'))
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="password-confirm" class="col-sm-5 col-form-label text-sm-right font-weight-bold">Passwort
                            best&auml;tigen:</label>

                        <div class="col-sm-6">
                            <input id="password-confirm" type="password" class="form-control"
                                   name="password_confirmation" required>
                        </div>
                    </div>

                    <!--
                    <div class="form-group row">
                        <label for="pin"
                               class="col-sm-5 col-form-label text-sm-right font-weight-bold">Einladungs-PIN:</label>

                        <div class="col-sm-6 d-flex justify-content-center">
                            <div>
                                <input id="pin" type="number"
                                       class="mx-auto form-control{{ $errors->has('pin') ? ' is-invalid' : '' }}"
                                       name="pin" value="{{ old('pin') }}" style="width: 120px !important;" required>

                                @if ($errors->has('pin'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('pin') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="ml-3 align-self-center">
                                <a data-container="body" data-toggle="popover" data-placement="top"
                                   data-content="Jeder bereits registrierte Spieler kann unter &quot;Sonstiges/Einladungen&quot; eine Einladungs-PIN erstellen.">
                                    <i class="fas fa-info-circle fa-lg my-auto text-dark"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                    -->

                    <div class="form-group row mb-0">
                        <div class="col-sm-6 offset-sm-5">
                            <button type="submit" class="btn btn-primary mt-2">
                                Registrieren
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            $('[data-toggle="popover"]').popover();
        });

        $('body').on('click', function (e) {
            $('[data-toggle=popover]').each(function () {
                if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                    $(this).popover('hide');
                }
            });
        });
    </script>
@endpush
