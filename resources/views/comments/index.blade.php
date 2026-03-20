<div class="mx-auto" style="max-width: 17rem;">
    <h4 class="mb-3" id="comments">Kommentare</h4>

    @include('include.error')

    {{ $comments->links() }}

    @include('comments.display')

    @include('comments.create', ['parentID' => 0, 'route' => $route])
</div>

@if(!$comments->onFirstPage())
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const comments = document.getElementById('comments');
                if (!comments) {
                    return;
                }

                const targetPosition = comments.getBoundingClientRect().top + window.scrollY - 50;
                window.scrollTo(0, targetPosition);
            });
        </script>
    @endpush
@endif
