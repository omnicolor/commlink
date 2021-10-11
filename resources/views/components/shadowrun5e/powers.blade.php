@if (0 !== count($powers))
<div class="card" id="powers-card">
    <div class="card-header">powers</div>
    <ul class="card-body list-group list-group-flush" id="powers">
    @foreach ($powers as $power)
        <li class="list-group-item">
            <span data-bs-placement="right" data-bs-toggle="tooltip"
                title="{{ $power->description }}">
                {{ $power }}
            </span>
        </li>
    @endforeach
    </ul>
</div>
@elseif ($charGen && $isAdept ?? false)
<div class="card" id="powers-card">
    <div class="card-header">powers</div>
    <ul class="card-body list-group list-group-flush" id="powers">
        <li class="list-group-item">
            @if ('mystic adept' === $type)
            <span class="badge rounded-pill bg-warning">!</span>
            @else
            <span class="badge rounded-pill bg-danger">!</span>
            @endif
            Your {{ $type }} has not picked up any powers. Empower them on
            the <a href="/characters/shadowrun5e/create/magic">magic page</a>.
        </li>
    </ul>
</div>
@endif
