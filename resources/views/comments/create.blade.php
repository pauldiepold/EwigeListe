<div x-data="{ open: false }">

    <button type="button" class="btn btn-outline-primary my-3" @click="open = true">
        Kommentar hinzufügen
    </button>

    <div x-show="open"
         x-transition.opacity
         @keydown.escape.window="open = false"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
         style="display: none;">
        <div class="bg-white rounded w-full max-w-sm mx-4 shadow-xl">
            <div class="modal-header py-2">
                <h4 class="modal-title mx-auto">Kommentar hinzufügen</h4>
            </div>
            <div class="modal-body">
                <form method="post" action="/comments">
                    @csrf
                    <textarea class="form-control" name="body" rows="3" id="bodytext" autofocus></textarea>
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
                <button type="button" class="btn btn-outline-secondary mx-auto" @click="open = false">Schließen</button>
            </div>
        </div>
    </div>
</div>
