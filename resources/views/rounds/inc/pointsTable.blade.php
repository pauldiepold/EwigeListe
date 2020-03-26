{{-- Input: collection $colRound --}}
<div class="row justify-content-center mt-4" v-touch:swipe.stop="">
    <div class="col col-xl-6 col-lg-8 col-md-10 col-sm-12">
        <div class="table-responsive">
            <table class="table mb-1">

                @php $header = $colRound->shift(); @endphp
                <tr class="border-bottom-thick">
                    @foreach ($header as $item)
                        <th class="tw-flex-col tw-items-center">
                            <div class="tw--mb-6 tw-text-transparent">asdf</div>
                            <img src="{{ $item->get(1) }}"
                                 class="tw-mx-auto tw-mb-1 tw-h-7 tw-w-7 tw-rounded-full">
                            <a class="{{ !$item->contains('dealer') ? 'text-dark ' : '' }}{{ $item->contains('active') ? 'active-player' : '' }}"
                               href="{{ $item->get(2) }}">{{$item->get(0)}}</a>
                        </th>
                    @endforeach
                    <th class="text-dark">Punkte</th>
                </tr>

                @foreach ($colRound as $row)
                    <tr class="{{ $row->contains('solo') ? 'bg-light' : ''}}{{ $row->contains('misplay') ? ' bg-danger-light ' : ''}}{{ $row->contains('endOfRound') ? 'border-bottom-thick' : ''}}">
                        @php
                            $row->contains('misplay') ? $row->pop() : '';
                            $row->contains('solo') ? $row->pop() : '';
                            $row->contains('endOfRound') ? $row->pop() : '';
                        @endphp

                        @foreach  ($row as $item)
                            <td class="{{ $item->count() == 2 ? 'font-weight-bold' : '' }}">
                                {{ $item->first() }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach

            </table>
        </div>
    </div>
</div>
