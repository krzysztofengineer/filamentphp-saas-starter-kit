@props([
    'command' => '',
    'size' => null,
])

@php
    $isLg = $size === 'lg';
    $wrapClasses = 'inline-flex items-center overflow-hidden rounded-[10px] border border-[var(--border)] bg-[var(--bg-elev)] transition-colors duration-150 hover:border-[var(--border-2)] [font-family:var(--mono)] '.($isLg ? 'text-[15px]' : 'text-[13px]');
    $promptPad = $isLg ? 'pl-4 pr-1' : 'pl-3.5 pr-1';
    $textPad = $isLg ? 'py-3.5 pr-4 pl-1.5' : 'py-2.5 pr-3.5 pl-1';
    $copyMinW = $isLg ? 'min-w-[50px]' : 'min-w-[42px]';
@endphp

<div
    class="{{ $wrapClasses }}"
    data-cmd="{{ $command }}"
    x-data="{
        copied: false,
        copy() {
            const text = this.$el.dataset.cmd ?? '';
            navigator.clipboard?.writeText(text);
            this.copied = true;
            clearTimeout(this._t);
            this._t = setTimeout(() => this.copied = false, 1400);
        },
    }"
>
    <span class="{{ $promptPad }} text-[var(--accent)] select-none">$</span>
    <span class="{{ $textPad }} whitespace-nowrap text-[var(--lp-text)]">{{ $command }}</span>
    <button
        type="button"
        class="group relative inline-flex items-center justify-center self-stretch border-l border-[var(--border)] px-3 text-[var(--text-3)] transition-colors duration-150 hover:bg-[var(--bg-soft)] hover:text-[var(--lp-text)] {{ $copyMinW }}"
        aria-label="Copy command"
        @click="copy()"
        :data-copied="copied ? 'true' : null"
    >
        <svg class="h-3.5 w-3.5 transition-opacity duration-150 group-data-[copied=true]:opacity-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/></svg>
        <span class="absolute inset-0 grid scale-[0.8] place-items-center text-[var(--accent)] opacity-0 transition-[opacity,transform] duration-150 group-data-[copied=true]:scale-100 group-data-[copied=true]:opacity-100">
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        </span>
    </button>
</div>
