@extends('layouts.main')

@section('title', 'Einladungen')

@section('heading', 'Einladungen')

@section('content')

    <p>
        Beim Registrieren neuer Spieler wird eine <b>Einladungs-PIN</b> benötigt. <br class="d-none d-md-block">Diese
        wird hier erstellt und eingesehen!
    </p>

    @include('include.error')
    @if(!$invitation)
        <form method="POST" action="/invitations">
            @csrf
            <button class="btn btn-primary my-2" type="submit">Einladungs-PIN erstellen</button>
        </form>
    @endif
    @if($invitation)
        <div class="card mx-auto my-3" style="width: 18rem;">
            <div class="card-header font-weight-bold">
                Einladungs-PIN
            </div>
            <div class="card-body">
                <h4 class="card-title  font-weight-bold">{{ $invitation->pin }}</h4>

                Gültig: <span id="demo"></span>
            </div>
            <div class="card-footer py-1">
                <form action="/invitations/{{ $invitation->id }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-danger btn btn-link"><i class="fas fa-trash fa-lg"></i></button>
                </form>
            </div>
        </div>

    @endif

    <script>
        // Set the date we're counting down to
        var countDownDate = new Date("{{ $invitation ? $invitation->valid_until : '' }}").getTime();

        // Update the count down every 1 second
        var x = setInterval(function () {

            // Get todays date and time
            var now = new Date().getTime();

            // Find the distance between now and the count down date
            var distance = countDownDate - now;

            // Time calculations for days, hours, minutes and seconds
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Display the result in the element with id="demo"
            document.getElementById("demo").innerHTML = hours + "h "
                + minutes + "m " + seconds + "s ";

            // If the count down is finished, write some text
            if (distance < 0) {
                clearInterval(x);
                document.getElementById("demo").innerHTML = "Abgelaufen";
            }
        }, 1000);
    </script>


@endsection