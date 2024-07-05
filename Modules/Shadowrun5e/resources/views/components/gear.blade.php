<div class="card" id="gearcard">
    <div class="card-header">gear</div>
    <table class="card-body table">
        <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Rating</th>
                <th scope="col">Qty</th>
            </tr>
        </thead>
        <tbody>
        @php
            $class = '';
        @endphp
        @forelse ($gears as $gear)
            @php
                if ($loop->last) {
                    $class = 'class="border-bottom-0"';
                }
            @endphp
            <tr>
                <td {!! $class !!}>
                    @can('view data')
                    <span data-bs-html="true" data-bs-toggle="tooltip"
                        title="<p>{!! str_replace(['||', '"'], ['</p><p>', '&quot;'], $gear->description) !!}</p>">
                        {{ $gear }}
                    </span>
                    @else
                        {{ $gear }}
                    @endcan
                    @if (!empty($gear->modifications))
                        <small class="text-muted">
                            @foreach ($gear->modifications as $mod)
                                @can('view data')
                                <span data-bs-html="true" data-bs-toggle="tooltip"
                                    title="<p>{!! str_replace(['||', '"'], ['</p><p>', '&quot;'], $mod->description) !!}</p>">
                                    {{ $mod }}@if ($mod->rating)
                                        ({{ $mod->rating }})
                                    @endif</span>@if (!$loop->last),@endif
                                @else
                                    {{ $mod }}@if ($mod->rating)
                                        ({{ $mod->rating }})
                                    @endif</span>@if (!$loop->last),@endif
                                @endcan
                            @endforeach
                        </small>
                    @endif
                </td>
                <td {!! $class !!}>{{ $gear->rating }}</td>
                <td {!! $class !!}>{{ $gear->quantity }}</td>
            </tr>
        @empty
            @if ($charGen)
                <tr>
                    <td class="border-bottom-0" colspan="3">
                        <span class="badge rounded-pill bg-warning ms-2">!</span>
                        No gear purchased. Buy some stuff on the
                        <a href="/characters/shadowrun5e/create/gear">gear page</a>.
                    </td>
                </tr>
            @else
                <tr>
                    <td class="border-bottom-0" colspan="3">No gear purchased.</td>
                </tr>
            @endif
        @endforelse
        </tbody>
    </table>
</div>
