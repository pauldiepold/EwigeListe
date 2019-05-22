
<button type="button" class="btn btn-primary my-3" data-toggle="modal" data-target="#createCommentModal">
  Kommentar hinzufügen
</button>

<div class="modal fade" id="createCommentModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h4 class="modal-title mx-auto">Kommentar schreiben</h4>
      </div>
      <div class="modal-body">
		  <form method="post" action="/comments">
	@csrf
		<textarea class="form-control" name="body"></textarea>
		<input type="hidden" name="round_id" value="{{ $round->id }}" />
    	<input type="submit" class="btn btn-primary mx-auto mt-3" value="Kommentar hinzufügen" />
		  </form>
      </div>
			  

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary mx-auto" data-dismiss="modal">Schließen</button>
            </div>
			  
    </div>
  </div>
</div>