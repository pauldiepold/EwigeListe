@component('mail::layout')
    {{-- Header --}}
    @if(false)
        @slot('header')
            @component('mail::header', ['url' => config('app.url')])
                {{ config('app.name') }}
            @endcomponent
        @endslot
    @endif

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} &bull; Paul Diepold
        @endcomponent
    @endslot
@endcomponent
