<div class="mx-auto" style="max-width: 20rem;">
    <h4>Kommentare</h4>
    @include('comments.display')

    @include('comments.create', ['parentID' => 0])

</div>