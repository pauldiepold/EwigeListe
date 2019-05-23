<button type="button" class="btn btn-primary my-3" data-toggle="modal" data-target="#createCommentModal">
    Kommentar hinzufügen
</button>

<div class="modal fade" id="createCommentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h4 class="modal-title mx-auto">Kommentar hinzufügen</h4>
            </div>
            <div class="modal-body">
                <form method="post" action="/comments">
                    @csrf
                    <textarea class="form-control" name="body" rows="3" id="bodytext" autofcus></textarea>
					@if(!strcmp($route, 'round'))
                        <input type="hidden" name="roundID" value="{{ $round->id }}"/>
                        <input type="hidden" name="route" value="{{ $route }}"/>
					@elseif(!strcmp($route, 'profile'))
                        <input type="hidden" name="profileID" value="{{ $profile->id }}"/>
                        <input type="hidden" name="route" value="{{ $route }}"/>
					@endif
                    <input type="submit" class="btn btn-primary mx-auto mt-3" value="Speichern"/>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary mx-auto" data-dismiss="modal">Schließen</button>
            </div>

        </div>
    </div>
</div>