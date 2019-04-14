@if ($errors->$nameBag->any())
    @foreach ($errors->$nameBag->all() as $error)
        <div class="alert alert-danger mb-3" role="alert">
            {{ $error }}
        </div>
    @endforeach
@endif