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
            @forelse ($weapons as $weapon)
                <tr>
                    <td data-bs-html="true" data-bs-placement="right"
                        data-toggle="tooltip" title="<p>{{ str_replace('||', '</p><p>', $weapon->description) }}</p>">
                        {{ $weapon }}
                        @if (!empty($weapon->modifications))
                            <br><small class="text-muted">
                                @foreach ($weapon->modifications as $mod)
                                    {{ $mod }}@if (!$loop->last), @endif
                                @endforeach
                            </small>
                        @endif
                    </td>
                    @if ('physical' == $weapon->accuracy)
                        <td>{{ $character->getPhysicalLimit() }}</td>
                    @else
                        <td>{{ $weapon->accuracy }}</td>
                    @endif
                    @if (is_array($weapon->modes))
                        <td>{{ implode(', ', $weapon->modes) }}</td>
                    @else
                        <td>{{ $weapon->modes }}</td>
                    @endif
                    @if ('firearm' === $weapon->type)
                        <td>{{ $weapon->getRange() }}</td>
                    @else
                        <td>{{ $weapon->reach }}</td>
                    @endif
                    <td>{{ $weapon->recoilCompensation }}</td>
                    <td>{{ $weapon->armorPiercing }}</td>
                    @if ('firearm' === $weapon->type)
                        <td>{{ $weapon->ammoCapacity }} ({{ $weapon->ammoContainer }})</td>
                    @else
                        <td></td>
                    @endif
                    <td>{{ $weapon->getDamage($character->getModifiedAttribute('strength')) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">
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
