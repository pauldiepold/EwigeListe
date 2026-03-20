<div x-data="{ visible: true }" x-show="visible" class="mb-4 max-w-md mx-auto">
    <div class="flex items-center justify-between px-4 py-3 rounded border bg-green-100 text-green-800 border-green-300">
        <span>{{ $slot }}</span>
        <button @click="visible = false" class="ml-4 text-green-600 hover:text-green-900" aria-label="Schließen">
            <i class="fas fa-sm fa-times"></i>
        </button>
    </div>
</div>
