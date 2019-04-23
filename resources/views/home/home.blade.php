@extends('layouts.main')

@section('title', 'Ewige Liste')

@section('heading', 'Startseite')

@section('content')

    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-9 col-lg-7 col-xl-6">
            <table class="table table-sm table-borderless text-left">
                @foreach($colFP as $row)
                    <tr>
                        <td{!! $row->contains('margin') ? ' class="pb-3"' : '' !!}>
                            {!! $row->shift() !!}
                        </td>
                        <td>
                            <b>{{ $row->shift() }}</b>
                        </td>
                        <td>
                            {!! $row->shift() !!}
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>

@endsection