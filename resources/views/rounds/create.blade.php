@extends('layouts.main')

@section('title', 'Runde Starten')

@section('heading', 'Neue Runde starten')

@section('content')
    <input class="typeahead form-control my-4" type="text">

    <script type="text/javascript">
        var path = "{{ route('autocomplete') }}";
        $('input.typeahead').typeahead({
            source: function (query, process) {
                return $.get(path, {query: query}, function (data) {
                    return process(data);
                });
            }
        });
    </script>

    <form method="POST" action="/rounds">
        @csrf

        @for ($k = 1; $k <= $numberOfPlayers; $k++)
            <div class="form-group mx-auto mt-4" style="max-width:200px;">
                <label for="player{{ $k }}"><b>Spieler {{ $k }}:</b></label>
                <select class="form-control" name="player{{ $k }}">
                    @foreach ($players as $player)
                        <option value="{{ $player->id }}" {{ (old("player$k") == $player->id ? "selected":"") }}>
                            {{ $player->surname }} {{ $player->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endfor

        <div class="row align-items-center mx-auto my-4" style="max-width:220px">

            <div class="col-2" style="padding:0px;">
                @if ($numberOfPlayers != 4)
                    <a href="/rounds/create/@php echo $numberOfPlayers-1; @endphp">
                        <i class="fas fa-minus-square fa-2x text-secondary"></i>
                    </a>
                @endif
            </div>

            <div class="col-8">
                <span style="font-size:1.15em">Spieleranzahl</span>
            </div>
            <div class="col-2" style="padding:0px;">
                @if ($numberOfPlayers != 7)
                    <a href="/rounds/create/@php echo $numberOfPlayers+1; @endphp">
                        <i class="fas fa-plus-square fa-2x text-secondary"></i>
                    </a>
                @endif
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Neue Runde Starten</button>
    </form>

    @include('include.error')

@endsection