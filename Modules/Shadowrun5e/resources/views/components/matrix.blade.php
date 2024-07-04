@if (0 !== count($devices) || $charGen)
<div class="card">
    <div class="card-header">matrix</div>
    <table class="card-body table">
        <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Rating</th>
                <th scope="col" title="Attack">A</th>
                <th scope="col" title="Sleaze">S</th>
                <th scope="col" title="Data Processing">D</th>
                <th scope="col" title="Firewall">F</th>
                <th scope="col">Programs</th>
            </tr>
        </thead>
        <tbody>
            @php
                $class = '';
            @endphp
            @forelse ($devices as $item)
                @php
                    if ($loop->last) {
                        $class = 'class="border-bottom-0"';
                    }
                @endphp
                <tr>
                    <td {!! $class !!}>
                        @if ($item->active ?? false)
                        <span class="oi oi-signal"></span>
                        @else
                        <span class="oi oi-signal text-muted"></span>
                        @endif
                        @can('view data')
                        <span data-bs-toggle="tooltip" data-bs-html="true"
                            title="<p>{{ str_replace('||', '</p><p>', $item->description) }}</p>">
                            {{ $item->subname }}
                            <small class="text-muted">({{ $item->name }})</small>
                        </span>
                        @else
                            {{ $item->subname }}
                            <small class="text-muted">({{ $item->name }})</small>
                        @endcan
                    </td>
                    <td {!! $class !!}>{{ $item->rating }}</td>
                    @foreach ($item->attributes as $stat)
                    <td {!! $class !!}>{{ $stat }}</td>
                    @endforeach
                    <td {!! $class !!}>{{ count($item->programs) }}</td>
                </tr>
                @empty
                <tr>
                    <td class="border-bottom-0" colspan="7">
                        <span class="badge rounded-pill bg-warning ms-2">!</span>
                        Character has no matrix devices. Wouldn't you like to
                        receive calls from your fixer? Buy a commlink on the
                        <a href="/characters/shadowrun5e/create/gear">gear page</a>.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif
