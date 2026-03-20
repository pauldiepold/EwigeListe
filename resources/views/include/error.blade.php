@if ($errors->any())
    @foreach ($errors->all() as $error)
        <div x-data="{ visible: true }" x-show="visible" class="my-2">
            <div class="flex items-center justify-between px-4 py-3 rounded border bg-red-100 text-red-800 border-red-300">
                <span>{{ $error }}</span>
                <button @click="visible = false" class="ml-4 text-red-600 hover:text-red-900" aria-label="Schließen">
                    <i class="fas fa-sm fa-times"></i>
                </button>
            </div>
        </div>
    @endforeach
@endif
