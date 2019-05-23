<div class="mx-auto" style="max-width: 17rem;">
	@include('include.error')
	
	{{ $comments->links() }}
	
    @include('comments.display', ['link' => true])

</div>