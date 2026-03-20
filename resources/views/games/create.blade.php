<div x-data="{ open: {{ count($errors->create) > 0 ? 'true' : 'false' }} }">

    <button type="button" class="btn btn-primary" @click="open = true">
        Spiel eintragen
    </button>

    <div x-show="open"
         x-transition.opacity
         @keydown.escape.window="open = false"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
         style="display: none;">
        <div class="bg-white rounded w-full max-w-lg mx-4 shadow-xl">
            <div class="modal-header">
                <h5 class="modal-title mx-auto" id="createModalLabel">Welche Personen haben gewonnen?</h5>
            </div>
            <div class="modal-body">

                @include('include.errorNamed', ['nameBag' => 'create'])

                <form method="POST" action="/rounds/{{ $round->id }}/game">
                    @csrf

                    @foreach ($activePlayers as $player)
                        <div class="flex items-center gap-2 my-1">
                            <input class="w-4 h-4 rounded border-gray-300 cursor-pointer"
                                   type="checkbox" value="{{ $player->id }}"
                                   id="player{{ $player->id }}" name="winners[]"
                                    {{ collect(old('winners'))->contains($player->id) ? 'checked' : '' }}>
                            <label class="cursor-pointer font-bold" for="player{{ $player->id }}">
                                {{ $player->surname }} {{ $player->name }}
                            </label>
                        </div>
                    @endforeach

                    <div class="flex justify-center my-4">
                        <input class="form-control w-24 text-center{{ $errors->create->first('points') ? ' border-red-400' : '' }}"
                               type="number" min="-4" max="16" name="points" value="{{ old('points') }}">
                        <label for="points" class="ml-2 self-center font-bold">Punkte</label>
                    </div>

                    <button type="submit" class="btn btn-primary">Bestätigen</button>
                    <hr class="my-3">

                    <div class="flex items-center gap-2 my-1">
                        <input class="w-4 h-4 rounded border-gray-300 cursor-pointer"
                               type="checkbox" value="1" id="misplayed" name="misplayed"
                                {{ old('misplayed') ? 'checked' : '' }}>
                        <label class="cursor-pointer font-bold" for="misplayed">
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
