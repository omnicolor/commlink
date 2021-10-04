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
        @forelse ($gears as $gear)
            @if (in_array($gear->name, ['Commlink', 'Cyberdeck', 'Rigger Command Console'], true))
                @continue
            @endif
            <tr>
                <td>
                    <span data-bs-html="true" data-bs-placement="right"
                        data-bs-toggle="tooltip"
                        title="<p>{!! str_replace(['||', '"'], ['</p><p>', '&quot;'], $gear->description) !!}</p>">
                        {{ $gear }}
                        @if (null !== $gear->subname)
                        &ndash; {{ $gear->subname }}
                        @endif
                        @if (!empty($gear->modifications))
                        <small class="text-muted">
                        @foreach ($gear->modifications as $mod)
                            {{ $mod }}@if (!$loop->last),@endif
                        @endforeach
                        </small>
                        @endif
                    </span>
                </td>
                <td>{{ $gear->rating }}</td>
                <td>{{ $gear->quantity }}</td>
            </tr>
        @empty
            @if ($charGen)
                <tr>
                    <td colspan="3">
                        <span class="badge rounded-pill bg-warning ms-2">!</span>
                        No gear purchased. Buy some stuff on the
                        <a href="/characters/shadowrun5e/create/gear">gear page</a>.
                    </td>
                </tr>
            @else
                <tr>
                    <td colspan="3">No gear purchased.</td>
                </tr>
            @endif
        @endforelse
        </tbody>
    </table>
</div>
