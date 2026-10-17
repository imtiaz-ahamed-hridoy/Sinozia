@props([
    'href' => 'javascript:history.back()',
    'class' => 'btn btn-secondary',
    'icon' => 'fas fa-chevron-left',
    'text' => 'Back'
])

<a {{ $attributes->merge(['class' => $class, 'href' => $href]) }}>
    <i class="{{ $icon }}"></i> {{ $text }}
</a>