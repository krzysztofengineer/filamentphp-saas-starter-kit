@php
    $rows = $data['rows'] ?? [
        ['name' => 'elena', 'activity' => 'scheduled deletion · 14d left', 'cursor' => ''],
        ['name' => 'jordan', 'activity' => 'cancelled · account restored', 'cursor' => 'g'],
        ['name' => 'rae', 'activity' => 'purged · pii erased', 'cursor' => 'b'],
    ];
@endphp

<div class="realtime-visual" aria-hidden="true">
    @foreach ($rows as $row)
        <div class="rt-row">
            <span class="rt-cursor @if (! empty($row['cursor'])) {{ $row['cursor'] }} @endif"></span>
            <span class="rt-name">{{ $row['name'] }}</span>
            <span class="rt-act">{{ $row['activity'] }}</span>
        </div>
    @endforeach
    <div class="rt-pulse"></div>
</div>
