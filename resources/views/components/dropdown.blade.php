@props(['align' => 'right', 'width' => 'w-48', 'contentClasses' => 'py-1 bg-white'])

<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <div @click="open = !open">{{ $trigger }}</div>
    <div x-show="open" x-transition class="absolute z-50 mt-2 {{ $width }} rounded-md shadow-lg {{ $align === 'left' ? 'left-0' : 'right-0' }}" style="display: none;" @click="open = false">
        <div class="rounded-md ring-1 ring-black ring-opacity-5 {{ $contentClasses }}">{{ $content }}</div>
    </div>
</div>
