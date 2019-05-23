@foreach($comments as $comment)
    <div class="card mb-3">
        <div class="card-header py-2">
            <strong>{{ $comment->createdBy->surname }}</strong> <span class="" style="font-size: 0.8rem;">- {{ printDate($comment->created_at) }}</span>
        </div>
        <div class="card-body py-2" style="white-space: pre-line">{{ $comment->body }}</div>
		@if(isset($link))
        <div class="card-footer py-2" style="font-size: 0.8rem;">
			@if(!strcmp($comment->commentable_type, 'App\Round'))
			in <a href="/rounds/{{ $comment->commentable_id }}">dieser</a> Runde
			@elseif(!strcmp($comment->commentable_type, 'App\Profile'))
			@php $surname = App\Profile::findOrFail($comment->commentable_id)->player->surname; @endphp
			in <a href="/profiles/{{ $comment->commentable_id }}">{{ $surname }}{{ strcmp(substr($surname, -1), 's') ? 's' : '' }}</a>  Profil
			@endif
        </div>
		@else
		@if(Auth()->user()->player->id == $comment->createdBy->id)
        <div class="card-footer py-2">
            <form action="/comments/{{ $comment->id }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-danger btn btn-link py-0" style="font-size: 0.8rem;">Kommentar löschen</button>
            </form>
        </div>
		@endif
		@endif
    </div>
@endforeach