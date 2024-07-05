@can('view data')
    <span data-bs-html="true" data-bs-toggle="tooltip"
        title="<p>{{ str_replace('||', '</p><p>', $mod->description) }}</p>">
        {{ $mod }}</span>@if (0 !== count($mod->modifications))
            (@foreach ($mod->modifications as $modMod)<span data-bs-html="true" data-bs-toggle="tooltip"
                    title="<p>{{ str_replace('||', '</p><p>', $modMod->description) }}</p>">{{ $modMod }}</span>@if (!$loop->last), @endif{{""}}@endforeach)
        @endif
@else
    {{ $mod }}@if (0 !== count($mod->modifications)):
        @foreach ($mod->modifications as $modMod)
            {{ $modMod }}</span>@if (!$loop->last), @endif
        @endforeach
    @endif
@endcan

@if (null !== $mod->weapon)
    <strong>Weapon: </strong>
    @can('view data')
    <span data-bs-html="true" data-bs-toggle="tooltip"
        title="<p>{{ str_replace('||', '</p><p>', $mod->weapon->description) }}</p>">
        {{ $mod->weapon }}
    </span>
    @else
    {{ $mod->weapon }}
    @endcan
@endif
