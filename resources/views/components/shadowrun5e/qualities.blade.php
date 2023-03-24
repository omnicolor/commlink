@if (0 !== count($qualities))
    <div class="card">
        <div class="card-header">qualities</div>
        <ul class="card-body list-group list-group-flush" id="qualities">
            @foreach ($qualities as $quality)
                <li class="list-group-item">
                    @can('view data')
                    <span data-bs-html="true" data-bs-toggle="tooltip"
                        title="<p>{{ str_replace('||', '</p><p>', $quality->description) }}</p>">
                        {{ $quality }}
                    </span>
                    @else
                        {{ $quality }}
                    @endcan
                </li>
            @endforeach
        </ul>
    </div>
@elseif ($charGen)
    <div class="card">
        <div class="card-header">qualities</div>
        <ul class="card-body list-group list-group-flush">
            <li class="list-group-item">
                <span class="badge rounded-pill bg-danger">!</span>
                No qualities found. Add them on the
                <a href="/characters/shadowrun5e/create/qualities">qualities page</a>.
            </li>
        </ul>
    </div>
@endif
