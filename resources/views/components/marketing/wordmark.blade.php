@props([
    'size' => null,
])

<span {{ $attributes->class(['marketing-wordmark', $size ? 'marketing-wordmark-' . $size : null]) }}>
    <span class="text-(--primary-600)">f</span><span class="opacity-55 mx-[0.04em]">.</span>aas
</span>
