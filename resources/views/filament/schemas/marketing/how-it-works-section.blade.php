@php
    $eyebrow = $getEyebrow();
    $heading = $getHeading();
    $description = $getDescription();
    $steps = $getSteps();
@endphp

<section class="section section-tight" id="how-it-works" data-testid="landing-how-it-works">
    <div class="container">
        <div class="section-head">
            @if (filled($eyebrow))
                <div class="eyebrow">{{ $eyebrow }}</div>
            @endif
            @if (filled($heading))
                <h2 class="section-h">{{ $heading }}</h2>
            @endif
            @if (filled($description))
                <p class="section-sub">{{ $description }}</p>
            @endif
        </div>

        <div class="steps">
            @foreach ($steps as $i => $step)
                <div class="step">
                    <div class="step-num">
                        <span class="n">{{ $i + 1 }}</span>
                        @if (! empty($step['kicker']))
                            <span>{{ $step['kicker'] }}</span>
                        @endif
                    </div>
                    @if (! empty($step['title']))
                        <h3>{!! $step['title'] !!}</h3>
                    @endif
                    @if (! empty($step['description']))
                        <p>{!! $step['description'] !!}</p>
                    @endif
                    @if (! empty($step['command']))
                        <code class="step-cmd"><span class="p">$</span> {{ $step['command'] }}</code>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
