@if ($errors->any())
    <div class="alert alert-danger my-3">
        <ul class="list-group ml-4">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif