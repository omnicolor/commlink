@if (0 !== count($augmentations))
    <div class="card">
        <div class="card-header">augmentations</div>
        <ul class="card-body list-group list-group-flush" id="augmentations">
            @foreach ($augmentations as $augmentation)
                <li class="list-group-item">
                    <span data-bs-html="true" data-bs-toggle="tooltip"
                        data-bs-placement="right"
                        title="<p>{{ str_replace('||', '</p><p>', $augmentation->description) }}</p>">
                        {{ $augmentation }}
                        @if (!is_null($augmentation->rating))
                            - {{ $augmentation->rating }}
                        @endif
                        @if (!is_null($augmentation->grade))
                            ({{ $augmentation->grade }})
                        @endif
                    </span>
                    @if (!empty($augmentation->modifications))
                        <ul class="list-group list-group-flush">
                            @foreach ($augmentation->modifications as $mod)
                                <li class="list-group-item">
                                    <span data-bs-toggle="tooltip" data-bs-placement="right"
                                        title="{{ $modification->description }}">
                                        {{ $modification }}
                                    </span>
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
        <div class="card-header">augmentations</div>
        <ul class="card-body list-group list-group-flush" id="augmentations">
            <li class="list-group-item">
                <span class="badge rounded-pill bg-warning">!</span>
                Character has no augmentations. Trade humanity for an edge on
                the <a href="/characters/shadowrun5e/create/augmentations">augmentations page</a>.
            </li>
        </ul>
    </div>
@endif
