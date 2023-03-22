@if (0 !== count($styles))
<div class="card" id="martial-arts-card">
    <div class="card-header">martial arts</div>
    <ul class="card-body list-group list-group-flush">
        @foreach ($styles as $style)
        <li class="list-group-item">
            <span data-bs-toggle="tooltip" data-bs-html="true"
                title="<p>{{ str_replace('||', '</p><p>', $style->description) }}</p>">
                {{ $style }} (style)
            </span>
        </li>
        @endforeach
        @forelse ($techniques as $technique)
        <li class="list-group-item">
            <span data-bs-toggle="tooltip" data-bs-html="true"
                title="<p>{{ str_replace('||', '</p><p>', $technique->description) }}</p>">
                {{ $technique }} (technique)
            </span>
        </li>
        @empty
            @if ($charGen)
                <li class="list-group-item">
                    <span class="badge rounded-pill bg-danger ms-2">!</span>
                    Character bought a martial art style, but didn't get the
                    free technique. Choose one on the
                    <a href="/characters/shadowrun5e/create/martial-arts">martial arts page</a>.
                </li>
            @endif
        @endforelse
    </ul>
</div>
@elseif ($charGen)
<div class="card" id="martial-arts-card">
    <div class="card-header">martial arts</div>
    <ul class="card-body list-group list-group-flush">
        <li class="list-group-item">
            <span class="badge rounded-pill bg-warning ms-2">!</span>
            Character has no martial arts. Learn some sweet moves on the
            <a href="/characters/shadowrun5e/create/martial-arts">martial arts page</a>.
        </li>
    </ul>
</div>
@endif
