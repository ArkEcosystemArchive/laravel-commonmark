@props([
    'url',
    'text',
    'class'     => 'link font-semibold',
    'inline'    => false,
    'allowWrap' => false,
    'small'     => false,
])<a
    href="{{ $url }}"
    class="{{ $class }} {{ $inline ? 'inline space-x-1' : 'flex items-center space-x-2' }} {{ $allowWrap ? '' : 'whitespace-nowrap' }}"
    target="_blank"
    rel="noopener nofollow noreferrer"
>
    <span>{{ $text }}</span>
</a>
