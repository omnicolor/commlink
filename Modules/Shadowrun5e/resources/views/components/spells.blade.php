@if (0 !== count($spells))
<div class="card" id="spells-card">
    <div class="card-header">spells</div>
    <table class="table" id="spells">
        <thead>
            <tr>
                <th scope="col">Spell</th>
                <th scope="col">Category</th>
                <th scope="col">Tags</th>
                <th scope="col">Dam</th>
                <th scope="col">Type</th>
                <th scope="col">Ran</th>
                <th scope="col">Dur</th>
                <th scope="col">Dra</th>
            </tr>
        </thead>
        <tbody style="font-size: 90%">
        @foreach ($spells as $spell)
            <tr>
                <td>
                    @can('view data')
                    <span data-bs-toggle="tooltip" data-bs-html="true"
                        title="<p>{{ str_replace('||', '</p><p>', $spell->description) }}</p>">
                        {{ $spell }}
                    </span>
                    @else
                        {{ $spell }}
                    @endcan
                </td>
                <td>{{ $spell->category }}</td>
                <td>{!! implode('<br>', $spell->tags) !!}</td>
                <td>{{ $spell->damage }}</td>
                <td>{{ $spell->type }}</td>
                <td>{{ $spell->range }}</td>
                <td>{{ $spell->duration }}</td>
                <td>{{ $spell->drain }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@elseif ($charGen)
<div class="card" id="spells-card">
    <div class="card-header">spells</div>
    <div class="card-body">
        <span class="badge rounded-pill bg-warning">!</span>
        Character has no spells. Learn some on the
        <a href="/characters/shadowrun5e/create/magic">magic page</a>.
    </div>
</div>
@endif
