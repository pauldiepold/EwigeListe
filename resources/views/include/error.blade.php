@if ($errors->any())
    @foreach ($errors->all() as $error)
        <div class="alert alert-danger my-2" role="alert">
            <div class="row align-items-center no-gutters">
                <div class="col-11">
                    {{ $error }}
                </div>
                <div class="col-1">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="fas fa-sm fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    @endforeach
@endif