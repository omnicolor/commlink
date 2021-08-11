<ul class="list-group">
    @if (0 === count($gmed) && 0 === count($registered) && 0 === count($playing))
        <li class="list-group-item">
            You don't have any campaigns!
        </li>
    @else
        @foreach ($gmed as $campaign)
            <li class="list-group-item">
                <a href="{{ route('campaign.view', ['campaign' => $campaign]) }}">
                    {{ $campaign }}</a>
                ({{ $campaign->getSystem() }}) -
                Gamemaster
            </li>
        @endforeach
        @foreach ($registered as $campaign)
            <li class="list-group-item">
                <a href="{{ route('campaign.view', ['campaign' => $campaign]) }}">
                    {{ $campaign }}</a>
                ({{ $campaign->getSystem() }}) -
                Registered
            </li>
        @endforeach
        @foreach ($playing as $campaign)
            @if ('accepted' === $campaign->pivot->status || 'invited' === $campaign->pivot->status)
            <li class="list-group-item">
                <a href="{{ route('campaign.view', ['campaign' => $campaign]) }}">
                    {{ $campaign }}</a>
                ({{ $campaign->getSystem() }}) -
                {{ ucfirst($campaign->pivot->status) }}
            </li>
            @endif
        @endforeach
    @endif
    <li class="list-group-item">
        <a class="btn btn-primary" href="/campaigns/create">
            Create campaign
        </a>
    </li>
</ul>
