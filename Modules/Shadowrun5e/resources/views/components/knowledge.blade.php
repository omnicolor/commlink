<div class="card">
    <div class="card-header">knowledge</div>
    <div class="card-body skills p-0">
        @forelse ($knowledges as $skill)
            <div class="row m-0 p-2 border-bottom">
                <div class="col text-truncate">
                    {{ $skill->name }}
                    @if ($skill->specialization)
                        ({{ $skill->specialization }})
                    @endif
                </div>
                <div class="col-1">{{ $skill->level }}</div>
                <div class="col-1 text-nowrap">{{ strtoupper(substr($skill->attribute, 0, 3)) }}</div>
                <div class="col-1 text-nowrap text-right">
                    {{ $character->getModifiedAttribute($skill->attribute) + $skill->level }}
                    [{{ $character->getSkillLimit($skill) }}]
                </div>
            </div>
        @empty
            @if ($charGen)
                <div class="row m-0 p-2 border-bottom">
                    <div class="col">
                        <span class="badge rounded-pill bg-danger">!</span>
                        No knowledge skills purchased. Add them on the
                        <a href="/characters/shadowrun5e/create/knowledge">knowledge page</a>.
                    </div>
                </div>
            @endif
        @endforelse
        @forelse ($languages as $skill)
            <div class="row m-0 p-2
            @if (!$loop->last)
                    border-bottom
                @endif
                ">
                <div class="col text-truncate">
                    {{ $skill->name }}
                    @if ($skill->specialization)
                        ({{ $skill->specialization }})
                    @endif
                </div>
                @if ('N' === $skill->level)
                <div class="col-3 text-center">Native</div>
                @else
                <div class="col-1">{{ $skill->level }}</div>
                <div class="col-1 text-nowrap">{{ strtoupper(substr($skill->attribute, 0, 3)) }}</div>
                <div class="col-1 text-nowrap text-right">
                    {{ $character->getModifiedAttribute($skill->attribute) + $skill->level }}
                    [{{ $character->getSkillLimit($skill) }}]
                </div>
                @endif
            </div>
        @empty
            @if ($charGen)
                <div class="row m-0 p-2 border-bottom">
                    <div class="col">
                        <span class="badge rounded-pill bg-danger">!</span>
                        No languages purchased. Add them on the
                        <a href="/characters/shadowrun5e/create/knowledge">knowledge page</a>.
                    </div>
                </div>
            @endif
        @endforelse
    </div>
</div>
