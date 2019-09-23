@extends('layouts.main')

@section('title', 'Ewige Liste')

@section('heading', 'Ewige Liste')

@section('content')
    <div class="row justify-content-center my-4">
        <div class="col col-xl-7 col-lg-8 col-md-9">
            <table class="table">
                <tr class="border-bottom-thick">
                    <th>
                        Name
                    </th>
                    <th>
                        Spiele
                    </th>
                </tr>

                @foreach ($players as $player)
                    <tr class="{{ $player->id == Auth::user()->player->id ? ' bg-primary-light' : ''}}"
                        style="{{ $player->payment == 1 ? 'background-color: #eeffe6;' : ''}}">
                        <td>
                            <a href="{{ $player->path() }}">
                                {{ $player->surname }} {{ $player->name }}
                            </a>
                        </td>
                        <td>
                            {{ $player->games->count() }}
                        </td>
                    </tr>
                @endforeach
            </table>
            <p class="mt-4">
                Anzahl registrierter Spieler: {{ $players->count() }}
            </p>
        </div>
    </div>
@endsection
