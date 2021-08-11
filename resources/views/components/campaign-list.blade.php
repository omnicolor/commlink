<ul class="list-group">
    @if (0 === count($gmed) && 0 === count($registered))
        <li class="list-group-item">
            You don't have any campaigns!
        </li>
    @else
        @foreach ($gmed as $campaign)
            <li class="list-group-item">
                {{ $campaign }}
                ({{ config('app.systems')[$campaign->system] }}) -
                Gamemaster
            </li>
        @endforeach
        @foreach ($registered as $campaign)
            <li class="list-group-item">
                {{ $campaign }}
                ({{ config('app.systems')[$campaign->system] }}) -
                Registered
            </li>
        @endforeach
    @endif
    <li class="list-group-item">
        <a class="btn btn-primary" href="/campaigns/create">
            Create campaign
        </a>
    </li>
</ul>
