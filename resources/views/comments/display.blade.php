@foreach($comments as $comment)
    <div class="card mb-3">
        <div class="card-header py-2 font-weight-bold">
            {{ $comment->createdBy->surname }} {{ $comment->createdBy->name }}
        </div>
        <div class="card-body py-2" style="white-space: pre-line">{{ $comment->body }}</div>
        <div class="card-footer py-2 px-2 text-muted" style="font-size: 0.8rem;">
			{{ printDate($comment->created_at) }}
		@if(isset($link))
			@if(!strcmp($comment->commentable_type, 'App\Round'))
			in <a href="/rounds/{{ $comment->commentable_id }}">dieser</a> Runde
			@elseif(!strcmp($comment->commentable_type, 'App\Profile'))
			@php $surname = App\Profile::findOrFail($comment->commentable_id)->player->surname; @endphp
			in <a href="/profiles/{{ $comment->commentable_id }}">{{ $surname }}{{ strcmp(substr($surname, -1), 's') ? 's' : '' }}</a>  Profil
			@endif
		@else
		@if(Auth()->user()->player->id == $comment->createdBy->id)
            <form action="/comments/{{ $comment->id }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-danger btn btn-link py-0" style="font-size: 0.8rem;">Kommentar löschen</button>
            </form>
		@endif
		@endif
        </div>
    </div>
@endforeach