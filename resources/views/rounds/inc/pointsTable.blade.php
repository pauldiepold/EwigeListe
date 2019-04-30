{{-- Input: collection $colRound --}}
<div class="row justify-content-center mt-4">
    <div class="col col-xl-6 col-lg-8 col-md-10 col-sm-12">
        <div class="table-responsive">
            <table class="table mb-1">

                @php $header = $colRound->shift(); @endphp
                <tr class="border-bottom-thick">
                    @foreach ($header as $item)
                        <th><a class="{{ !$item->contains('dealer') ? 'text-dark ' : '' }}{{ $item->contains('active') ? 'active-player' : '' }}" href="/profiles/{{ $item->get('1') }}">{{$item->first()}}</a></th>
                    @endforeach
                    <th class="text-dark">Punkte</th>
                </tr>

                @foreach ($colRound as $row)
                    <tr class="{{ $row->contains('solo') ? 'bg-light' : ''}}{{ $row->contains('misplay') ? 'bg-danger' : ''}}{{ $row->contains('endOfRound') ? 'border-bottom-thick' : ''}}">
                        @php $row->contains('solo') || $row->contains('endOfRound') ? $row->pop() : ''; @endphp

                        @foreach  ($round->players as $player)
                            @php $item = $row->get($player->id); @endphp
                            <td {!! $item->contains('won') ? ' class="font-weight-bold"' : '' !!}>
                                {{ $item->first() }}
                            </td>
                        @endforeach

                        <td>{{ $row->get('points')->first() }}</td>

                    </tr>
                @endforeach

            </table>
        </div>
    </div>
</div>