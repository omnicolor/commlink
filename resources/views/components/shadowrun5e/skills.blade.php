<div class="card">
    <div class="card-header">skills</div>
    <div class="card-body skills p-0">
        @forelse ($skillGroups as $group)
            <div class="row m-0 p-2">
                <div class="col">{{ $group }} Group - {{ $group->level }}</div>
            </div>
            @foreach ($group->skills as $skill)
                <div class="row m-0 p-2 @if ($loop->last) border-bottom @endif ">
                    <div class="col ms-4 ps-4 text-truncate" data-bs-html="true"
                        data-bs-toggle="tooltip" data-bs-placement="right"
                        title="<p>{{ str_replace('||', '</p><p>', $skill->description) }}</p>">
                        {{ $skill }}
                    </div>
                    <div class="col-1">{{ $group->level }}</div>
                    <div class="col-1 text-nowrap">
                        {{ strtoupper(substr($skill->attribute, 0, 3)) }}
                    </div>
                    <div class="col-3 text-nowrap text-right">
                        {{ $character->getModifiedAttribute($skill->attribute) + $group->level }}
                        [{{ $character->getSkillLimit($skill) }}]
                    </div>
                </div>
            @endforeach
        @empty
            @if ($charGen)
                <div class="row m-0 p-2 border-bottom">
                    <div class="col">
                        <span class="badge rounded-pill bg-danger">!</span>
                        No skill groups purchased. Add them on the
                        <a href="/characters/shadowrun5e/create/skills">skills page</a>.
                    </div>
                </div>
            @endif
        @endforelse
        @forelse ($skills as $skill)
            <div class="row m-0 p-2
            @if (!$loop->last)
                    border-bottom
                @endif
                ">
                <div class="col text-truncate" data-bs-toggle="tooltip"
                    data-bs-placement="right" data-bs-html="true"
                    title="<p>{{ str_replace('||', '</p><p>', $skill->description) }}</p>">
                    {{ $skill->name }}
                    @if ($skill->specialization)
                        ({{ $skill->specialization }})
                    @endif
                </div>
                <div class="col-1">{{ $skill->level }}</div>
                <div class="col-1 text-nowrap">{{ strtoupper(substr($skill->attribute, 0, 3)) }}</div>
                <div class="col-3 text-nowrap text-right">
                    {{ $character->getModifiedAttribute($skill->attribute) + $skill->level }}
                    [{{ $character->getSkillLimit($skill) }}]
                </div>
            </div>
        @empty
            @if ($charGen)
                <div class="row m-0 p-2 border-bottom">
                    <div class="col">
                        <span class="badge rounded-pill bg-danger">!</span>
                        No skills purchased. Add them on the
                        <a href="/characters/shadowrun5e/create/skills">skills page</a>.
                    </div>
                </div>
            @endif
        @endforelse
    </div>
</div>
