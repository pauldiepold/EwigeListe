@extends('layouts.main')

@section('title', 'Kontoeinstellungen')

@section('heading', 'Kontoeinstellungen')

@section('content')

    @if (session('message'))
        @component('include.success')
            {{ session('message') }}
        @endcomponent
    @endif
    <div id="alertPlaceholder"></div>

    <tabs>

        <tab name="listen" icon="fa-list-alt" :selected="true">
            <template v-slot:default="props">

                <h5 class="tw-mb-4 tw-font-bold">Standard-Listen auswählen</h5>

                <p class="tw-max-w-xs tw-mx-auto">
                    Jede deiner Listen kann als Standard Liste ausgewählt werden. Zukünftig wird sie beim Start einer neuen Runde automatisch aktiviert.
                </p>

                <standard-groups :profiles-input="{{ $profiles }}"
                                 :player-id="{{ auth()->user()->player->id }}"
                                 :key="props.tabKey">
                </standard-groups>
            </template>
        </tab>

        <tab name="name" icon="fa-user">

            <h5 class="tw-mb-6 tw-font-bold">Namen ändern</h5>

            <form class="tw-max-w-xs tw-mx-auto" method="POST" action="{{ route('players.updateName', [$player]) }}">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label for="vorname">Vorname:</label>
                    <input type="text" class="form-control {{ $errors->has('vorname') ? 'is-invalid' : '' }}"
                           value="{{ old('vorname') ? old('vorname') : $player->surname }}"
                           name="vorname" required>
                    @if ($errors->has('vorname'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('vorname') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group">
                    <label for="nachname">Nachname:</label>
                    <input type="text" class="form-control {{ $errors->has('nachname') ? 'is-invalid' : '' }}"
                           value="{{ old('nachname') ? old('nachname') : $player->name }}"
                           name="nachname" required>
                    @if ($errors->has('nachname'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('nachname') }}</strong>
                        </span>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary">Namen speichern</button>
            </form>
        </tab>

        <tab name="email" icon="fa-at">

            <h5 class="tw-mb-6 tw-font-bold">E-Mail ändern</h5>

            <form class="tw-max-w-xs tw-mx-auto" method="POST" action="{{ route('players.updateMail', [$player]) }}">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label for="email">E-Mail:</label>
                    <input type="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                           value="{{ old('email') ? old('email') : $player->user->email }}"
                           name="email" required>
                    @if ($errors->has('email'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary">E-Mail speichern</button>
            </form>
        </tab>

        <tab name="passwort" icon="fa-key">

            <h5 class="tw-mb-6 tw-font-bold">Passwort ändern</h5>

            <form class="tw-max-w-xs tw-mx-auto" method="POST"
                  action="{{ route('players.updatePassword', [$player]) }}">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label for="current_password">Altes Passwort:</label>
                    <input type="password"
                           class="form-control {{ $errors->has('current_password') ? 'is-invalid' : '' }}"
                           value="{{ old('current_password') }}"
                           placeholder="*********" name="current_password" required>
                    @if ($errors->has('current_password'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('current_password') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group">
                    <label for="password">Neues Passwort:</label>
                    <input type="password"
                           class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                           value="{{ old('password') }}"
                           placeholder="*********" name="password" required>
                    @if ($errors->has('password'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Passwort bestätigen:</label>
                    <input type="password"
                           class="form-control {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
                           value="{{ old('password_confirmation') }}"
                           placeholder="*********" name="password_confirmation" required>
                    @if ($errors->has('password_confirmation'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                        </span>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary">Passwort speichern</button>
            </form>
        </tab>
    </tabs>

@endsection
