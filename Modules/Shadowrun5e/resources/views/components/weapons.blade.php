<div class="card" id="weaponlist">
    <div class="card-header">weapons</div>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">name</th>
                <th scope="col">acc</th>
                <th scope="col">modes</th>
                <th scope="col">ranges/reach</th>
                <th scope="col">recoil</th>
                <th scope="col">piercing</th>
                <th scope="col">ammo</th>
                <th scope="col">damage</th>
            </tr>
        </thead>
        <tbody>
            @php
                $class = '';
            @endphp
            @forelse ($weapons as $weapon)
                @php
                    if ($loop->last) {
                        $class = 'class="border-bottom-0"';
                    }
                @endphp
                <tr>
                    <td {!! $class !!}">
                        @can('view data')
                        <span data-bs-html="true" data-bs-toggle="tooltip"
                            title="<p>{{ str_replace('||', '</p><p>', $weapon->description) }}</p>">
                            {{ $weapon }}
                        </span>
                        @else
                            {{ $weapon }}
                        @endcan
                        @if (!empty($weapon->modifications))
                            <br><small class="text-muted">
                                @foreach ($weapon->modifications as $mod)
                                    @can('view data')
                                    <span data-bs-html="true" data-bs-toggle="tooltip"
                                        title="<p>{{ str_replace('||', '</p><p>', $mod->description) }}</p>">
                                        {{ $mod }}</span>@if (!$loop->last), @endif
                                    @else
                                        {{ $mod }}@if (!$loop->last), @endif
                                    @endcan
                                @endforeach
                            </small>
                        @endif
                    </td>
                    @if ('physical' == $weapon->accuracy)
                        <td {!! $class !!}>{{ $character->getPhysicalLimit() }}</td>
                    @else
                        <td {!! $class !!}>{{ $weapon->accuracy }}</td>
                    @endif
                    @if (is_array($weapon->modes))
                        <td {!! $class !!}>{{ implode(', ', $weapon->getModes()) }}</td>
                    @else
                        <td {!! $class !!}>{{ $weapon->modes }}</td>
                    @endif
                    @if ('firearm' === $weapon->type)
                        <td {!! $class !!}>{{ $weapon->getRange() }}</td>
                    @else
                        <td {!! $class !!}>{{ $weapon->reach }}</td>
                    @endif
                    <td {!! $class !!}>{{ $weapon->recoil_compensation }}</td>
                    <td {!! $class !!}>{{ $weapon->armor_piercing }}</td>
                    @if ('firearm' === $weapon->type)
                        <td {!! $class !!}>{{ $weapon->ammo_capacity }} ({{ $weapon->ammo_container }})</td>
                    @else
                        <td {!! $class !!}></td>
                    @endif
                    <td {!! $class !!}>{{ $weapon->getDamage($character->getModifiedAttribute('strength')) }}</td>
                </tr>
            @empty
                <tr>
                    <td class="border-bottom-0" colspan="8">
                        <span class="badge rounded-pill bg-danger ms-2">!</span>
                        Character is unarmed.
                        @if ($charGen)
                            Purchase weapons on the <a href="/characters/shadowrun5e/create/weapons">weapons page</a>.
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
