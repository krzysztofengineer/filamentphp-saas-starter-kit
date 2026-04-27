@php
    $rows = $data['rows'] ?? [
        ['name' => 'elena', 'activity' => 'editing invoice #2041', 'cursor' => ''],
        ['name' => 'jordan', 'activity' => 'viewing dashboard', 'cursor' => 'b'],
        ['name' => 'rae', 'activity' => 'joined presence channel', 'cursor' => 'g'],
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
