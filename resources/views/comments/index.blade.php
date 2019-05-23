<div class="mx-auto" style="max-width: 20rem;">
    <h4 class="mb-3" id="comments">Kommentare</h4>
	
	@include('include.error')
	
	{{ $comments->links() }}
	
    @include('comments.display')

    @include('comments.create', ['parentID' => 0, 'route' => $route])

</div>

    @if(!$comments->onFirstPage())
        @push('scripts')
            <script>
                $(document).ready(function () {
                    $('html, body').scrollTop($('#comments').offset().top - 50);
                });
            </script>
        @endpush
    @endif