@props([
    'command' => '',
    'size' => null,
])

<div
    class="cmd @if ($size === 'lg') cmd-lg @endif"
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
    <span class="cmd-prompt">$</span>
    <span class="cmd-text">{{ $command }}</span>
    <button
        type="button"
        class="cmd-copy"
        aria-label="Copy command"
        @click="copy()"
        :data-copied="copied ? 'true' : null"
    >
        <svg class="copy-default" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/></svg>
        <span class="copied"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
    </button>
</div>
