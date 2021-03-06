@extends('layouts.main')

@section('title', 'Registrieren')

@section('heading', 'Registrieren')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm col-md-11 col-lg-8">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <a href="{{ route('auth.socialite', ['provider' => 'facebook']) }}"
                       class="btn btn-outline-primary mt-2 tw-w-64">
                        <div class="tw-flex tw-items-center">
                            <i class="fab fa-facebook tw-text-2xl tw-mr-2" style="color: #3b5998;"></i>
                            <span class="tw-flex-1">Mit Facebook registrieren</span>
                        </div>
                    </a>

                    <br>

                    <a href="{{ route('auth.socialite', ['provider' => 'google']) }}"
                       class="btn btn-outline-primary my-2 tw-w-64">
                        <div class="tw-flex tw-items-center">
                            <i class="fab fa-google tw-text-2xl tw-mr-2 tw-text-gray-700"></i>
                            <span class="tw-flex-1">Mit Google registrieren</span>
                        </div>
                    </a>

                    <hr>
                    <p class="tw-font-bold">Oder mit E-Mail registrieren:</p>

                    <div class="form-group row">
                        <label for="email"
                               class="col-sm-5 col-form-label text-sm-right">E-Mail</label>

                        <div class="col-sm-6">
                            <input id="email" type="email"
                                   class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                   name="email"
                                   value="{{ old('email') }}" required>

                            @if ($errors->has('email'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="surname"
                               class="col-sm-5 col-form-label text-sm-right">Vorname</label>

                        <div class="col-sm-6">
                            <input id="surname" type="text"
                                   class="form-control{{ $errors->has('surname') ? ' is-invalid' : '' }}"
                                   name="surname"
                                   value="{{ old('surname') }}" required autofocus>

                            @if ($errors->has('surname'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('surname') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="name" class="col-sm-5 col-form-label text-sm-right">Name</label>

                        <div class="col-sm-6">
                            <input id="name" type="text"
                                   class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                   name="name"
                                   value="{{ old('name') }}" required>

                            @if ($errors->has('name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="password"
                               class="col-sm-5 col-form-label text-sm-right">Passwort</label>

                        <div class="col-sm-6">
                            <input id="password" type="password"
                                   class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                   name="password"
                                   value="{{ old('password') }}" required>

                            @if ($errors->has('password'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="password-confirm" class="col-sm-5 col-form-label text-sm-right">Passwort best&auml;tigen</label>

                        <div class="col-sm-6">
                            <input id="password-confirm" type="password" class="form-control"
                                   name="password_confirmation" required>
                        </div>
                    </div>

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
