@extends('layouts.main')

@section('title', 'Runde Starten')

@section('heading', 'Neue Runde starten')

@section('content')

    <div class="row align-items-center mx-auto my-4 no-gutters" style="max-width:230px">

        <div class="col-2 p-0">
            <a @click="form.numberOfPlayers--" v-if="form.numberOfPlayers > 4">
                <i class="fas fa-minus-square fa-2x text-primary"></i>
            </a>
        </div>

        <div class="col-8">
            <span class="text-dark" style="font-size:1.15em">
                <i v-for="index in form.numberOfPlayers" class="fas fa-user" style="margin: 0 0.1rem;"></i>
            </span>
        </div>

        <div class="col-2 p-0">
            <a @click="form.numberOfPlayers++" v-if="form.numberOfPlayers < 7">
                <i class="fas fa-plus-square fa-2x text-primary"></i>
            </a>
        </div>

    </div>


    <alert v-if="form.errors.any()" v-bind:message="form.errors.get('players')"></alert>

    <form @submit.prevent="onSubmit">
        @csrf

        <input type="hidden" v-model="form.numberOfPlayers">

        @for ($k = 0; $k < 7; $k++)
            <div v-if="{{ $k }} < form.numberOfPlayers" class="form-group mx-auto mt-4" style="max-width:200px;">
                <label for="player{{ $k }}"><b>Spieler {{ $k+1 }}:</b></label>
                <select class="form-control" name="players[{{ $k }}]" v-model="form.players[{{ $k }}]">
                    @foreach ($players as $player)
                        <option value="{{ $player->id }}" {{ (old("players")[$k] == $player->id || ($loop->index == $k && old("players")[$k] == null) ? "selected" : "") }}>
                            {{ $player->surname }} {{ $player->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endfor

        <button type="submit" class="btn btn-primary mt-3">
            <div class="d-flex vertical-align-center">
                 <span>
                 <i v-if="form.loading" class="fa fa-spinner fa-spin text-lg mr-2"
                    style="font-size:1.2rem; vertical-align: -0.1rem;"></i>
                 </span>
                <span>
                    Neue Runde starten
                 </span>
            </div>
        </button>
    </form>
    <div class="mt-4">
        <a data-container="body" data-toggle="popover" data-placement="top"
           data-content="Die Spieler entsprechend ihrer Sitzreihenfolge auswählen. Spieler 1 beginnt als Geber!">
            <i class="fas fa-info-circle fa-lg"></i>
        </a>
    </div>


    @php
        $preselectedPlayers = array();
        for($k = 0; $k < 7; $k++) {
            $preselectedPlayers[$k] = $players[$k]->id;
        }
    @endphp
    @push('scriptsBeforeJS')
        <script>
            let preselectedPlayers = {{ json_encode($preselectedPlayers) }};
        </script>
    @endpush

@endsection