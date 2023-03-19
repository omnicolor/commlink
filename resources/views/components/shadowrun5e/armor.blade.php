@if (0 !== count($armors))
<div class="card">
    <div class="card-header">
        armor: <span id="armor-value">{{ $character->getArmorValue() }}</span>
    </div>
    <ul class="card-body list-group list-group-flush" id="armor">
    @foreach ($armors as $key => $armor)
        <li class="list-group-item" data-index="{{ $key }}"
            data-stack="{{ $armor->stackRating }}">
            @if ($armor->active)
            <span class="oi oi-shield"></span>
            @else
            <span class="oi oi-shield text-muted"></span>
            @endif
            @can('view data')
            <span data-bs-html="true" data-bs-toggle="tooltip"
                title="<p>{{ str_replace('||', '</p><p>', $armor->description) }}</p>">
                {{ $armor }}
            </span>
            @else
                {{ $armor }}
            @endcan
            <div class="value">{{ $armor->rating }}</div>
            @if ($armor->modifications)
                <ul class="list-group list-group-flush">
                @foreach ($armor->modifications as $modification)
                    <li class="list-group-item">
                        @can('view data')
                        <span data-bs-toggle="tooltip" data-bs-html="true"
                            title="<p>{{ str_replace('||', '</p><p>', $modification->description) }}</p>">
                            {{ $modification }}
                        </span>
                        @else
                            {{ $modification }}
                        @endcan
                        <div class="value">{{ $modification->rating }}</div>
                    </li>
                @endforeach
                </ul>
            @endif
        </li>
    @endforeach
    </ul>
</div>
@elseif ($charGen)
<div class="card">
    <div class="card-header">
        armor: <span id="armor-value">0</span>
    </div>
    <ul class="card-body list-group list-group-flush" id="armor">
        <li class="list-group-item">
            <span class="badge rounded-pill bg-danger ms-2">!</span>
            Character has no armor. While the bad guys appreciate it, you should
            probably <a href="/characters/shadowrun5e/create/armor">protect
            yourself</a>.
        </li>
    </ul>
</div>
@endif
