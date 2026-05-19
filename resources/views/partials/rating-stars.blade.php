@props([
    'rating' => 0,
    'max' => 5,
    'size' => 'md',
])

@php
    $filled = max(0, min((int) $max, (int) round((float) $rating)));
    $sizes = [
        'sm' => '16px',
        'md' => '20px',
        'lg' => '24px',
    ];
    $px = $sizes[$size] ?? $sizes['md'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-0.5 align-middle']) }} role="img" aria-label="{{ $filled }} out of {{ $max }} stars">
    @for ($i = 1; $i <= $max; $i++)
        <svg style="width:{{ $px }}; height:{{ $px }}; flex-shrink:0;" fill="{{ $i <= $filled ? '#ffc107' : 'none' }}" stroke="{{ $i <= $filled ? '#ffc107' : '#ddd' }}" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
        </svg>
    @endfor
</span>
