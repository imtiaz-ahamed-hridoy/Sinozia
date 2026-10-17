@props(['messages' => null, 'for' => null])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-sm text-danger space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@elseif ($for)
    @error($for)
        <p {{ $attributes->merge(['class' => 'text-sm text-danger']) }}>{{ $message }}</p>
    @enderror
@endif
