@foreach($comments as $comment)
    <div class="card mb-3">
        <div class="card-header py-2">
            <strong>{{ $comment->createdBy->surname }}</strong> - {{ printDate($comment->created_at) }}
        </div>
        <div class="card-body py-2" style="white-space: pre-line">{{ $comment->body }}</div>
        <div class="card-footer py-2">
            <form action="/comments/{{ $comment->id }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-danger btn btn-link py-0">Kommentar löschen</button>
            </form>
        </div>
    </div>
@endforeach