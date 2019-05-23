<div class="mx-auto" style="max-width: 20rem;">
    <h5 class="mt-4 mb-3 font-weight-bold" style="font-family:Raleway" id="comments">Neue Kommentare</h5>
	
	@include('include.error')
	
	{{ $comments->links() }}
	
    @include('comments.display', ['link' => true])

</div>