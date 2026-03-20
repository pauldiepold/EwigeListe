<div x-data="{ open: {{ count($errors->update) > 0 ? 'true' : 'false' }} }">

    <button type="button" class="btn btn-outline-primary" @click="open = true">
        Letztes Spiel ändern
    </button>

    <div x-show="open"
         x-transition.opacity
         @keydown.escape.window="open = false"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
         style="display: none;">
        <div class="bg-white rounded w-full max-w-lg mx-4 shadow-xl">
            <div class="modal-header">
                <h5 class="modal-title mx-auto">Letztes Spiel ändern</h5>
            </div>
            <div class="modal-body">

                @include('include.errorNamed', ['nameBag' => 'update'])

                <form method="POST" action="/games/{{ $lastGame->id }}">
                    @csrf
                    @method('PATCH')

                    @foreach ($lastGame->players as $player)
                        <div class="flex items-center gap-2 my-1">
                            <input class="w-4 h-4 rounded border-gray-300 cursor-pointer"
                                   type="checkbox" value="{{ $player->id }}"
                                   id="updatePlayer{{ $player->id }}" name="updateWinners[]"
                                    {{ $player->pivot->won ? 'checked' : '' }}>
                            <label class="cursor-pointer font-bold" for="updatePlayer{{ $player->id }}">
                                {{ $player->surname }} {{ $player->name }}
                            </label>
                        </div>
                    @endforeach

                    <div class="flex justify-center my-4">
                        <input class="form-control w-24 text-center{{ $errors->update->first('points') ? ' border-red-400' : '' }}"
                               type="number" min="-4" max="16" name="updatePoints"
                               value="{{ $lastGame->points }}">
                        <label for="updatePoints" class="ml-2 self-center font-bold">Punkte</label>
                    </div>

                    <button type="submit" class="btn btn-primary">Bestätigen</button>
                    <hr class="my-3">

                    <div class="flex items-center gap-2 my-1">
                        <input class="w-4 h-4 rounded border-gray-300 cursor-pointer"
                               type="checkbox" value="1" id="updateMisplayed" name="updateMisplayed"
                                {{ $lastGame->misplay ? 'checked' : '' }}>
                        <label class="cursor-pointer font-bold" for="updateMisplayed">
                            Falsch bedient?
                        </label>
                        <span class="text-gray-500 text-sm ml-1"
                              title="Falls jemand falsch bedient, wird dies als verlorenes Solo mit 2 Punkten plus die getätigten Ansagen gewertet.">
                            <i class="fas fa-info-circle"></i>
                        </span>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary mx-auto" @click="open = false">Schließen</button>
            </div>
        </div>
    </div>
</div>
