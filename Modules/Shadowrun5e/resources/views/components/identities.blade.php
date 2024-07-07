@if (0 !== count($identities) || $charGen)
<div class="card">
    <div class="card-header">identities</div>
    <ul class="card-body list-group list-group-flush" id="identities">
    @forelse ($identities as $key => $identity)
        <li class="list-group-item">
            {{ $identity->name }}
            <ul class="card-body list-group list-group-flush">
                @if (null === $identity->sin && null === $identity->sinner)
                <li class="list-group-item">
                    <span class="badge rounded-pill bg-danger ms-2">!</span>
                    Identity has no SIN.
                    <a href="/characters/shadowrun5e/create/social">Purchase a fake</a>.
                </li>
                @elseif (null !== $identity->sin)
                <li class="list-group-item">Fake SIN ({{ $identity->sin }})</li>
                @else
                <li class="list-group-item">SIN ({{ $identity->sinner }})</li>
                @endif
                @foreach ($identity->lifestyles as $lifestyle)
                <li class="list-group-item">
                    {{ $lifestyle }} lifestyle - {{ $lifestyle->quantity }}
                    {{ \Str::of('month')->plural($lifestyle->quantity) }}
                    @if (0 !== count($lifestyle->options))
                        <br>
                        <small class="text-muted">
                        @foreach ($lifestyle->options as $option)
                            {{ $option }}@if (!$loop->last), @endif
                        @endforeach
                        </small>
                    @endif
                </li>
                @endforeach
                @foreach ($identity->licenses as $license)
                <li class="list-group-item">
                    {{ $license }} license
                </li>
                @endforeach
                @if (null !== $identity->notes && '' !== $identity->notes)
                    <li class="list-group-item">{{ $identity->notes }}</li>
                @endif
            </ul>
        </li>
    @empty
        <li class="list-group-item">
            <span class="badge rounded-pill bg-danger">!</span>
            Character has no identities. If you don't have a
            <a href="/characters/shadowrun5e/create/social">fake SIN</a>,
            the authorities will helpfully issue you a Criminal SIN.
        </li>
    @endforelse
    </ul>
</div>
@endif
