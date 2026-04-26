@php
    $items = [
        ['label' => 'Features', 'url' => '/#features'],
        ['label' => 'How it works', 'url' => '/#how-it-works'],
        ['label' => 'Pricing', 'url' => '/#pricing'],
        ['label' => 'FAQ', 'url' => '/#faq'],
    ];
@endphp
<ul class="fi-topbar-nav-groups" style="display:flex;align-items:center;gap:1.5rem;">
    @foreach ($items as $item)
        <li class="fi-topbar-item">
            <a href="{{ $item['url'] }}" class="fi-topbar-item-btn">
                <span class="fi-topbar-item-label">{{ $item['label'] }}</span>
            </a>
        </li>
    @endforeach
</ul>
