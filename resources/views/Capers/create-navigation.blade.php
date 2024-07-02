<x-slot name="navbar">
    <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard') }}">Home</a>
    </li>
    <li class="nav-item dropdown">
        <a aria-expanded="false" class="active nav-link dropdown-toggle"
            data-bs-toggle="dropdown" href="#" id="creating-dropdown"
            role="button">
            Creating
            @if ($character->name)
                &ldquo;{{ $character }}&rdquo;
            @else
                new character
            @endif
        </a>
        <ul class="dropdown-menu" aria-labelledby="creating-dropdown">
            <li><a class="dropdown-item @if ('basics' === $creating) active @endif"
                href="{{ route('capers.create') }}/basics">
                Basics
            </a></li>
            <li><a class="dropdown-item @if ('anchors' === $creating) active @endif"
                href="{{ route('capers.create') }}/anchors">
                Anchors
            </a></li>
            <li><a class="dropdown-item @if ('traits' === $creating) active @endif"
                href="{{ route('capers.create') }}/traits">
                Traits
            </a></li>
            <li><a class="dropdown-item @if ('skills' === $creating) active @endif"
                href="{{ route('capers.create') }}/skills">
                Skills
            </a></li>
            @if (\App\Models\Capers\Character::TYPE_CAPER === $character->type)
            <li><a class="dropdown-item @if ('powers' === $creating) active @endif"
                href="{{ route('capers.create') }}/powers">
                Powers
            </a></li>
                @if (0 < count($character->powers))
                <li><a class="dropdown-item @if ('boosts' === $creating) active @endif"
                    href="{{ route('capers.create') }}/boosts">
                    Boosts
                </a></li>
                @endif
            @elseif (\App\Models\Capers\Character::TYPE_EXCEPTIONAL === $character->type)
            <li><a class="dropdown-item @if ('perks' === $creating) active @endif"
                href="{{ route('capers.create') }}/perks">
                Perks and trem-gear
            </a></li>
            @endif
            <li><a class="dropdown-item @if ('gear' === $creating) active @endif"
                href="{{ route('capers.create') }}/gear">
                Gear
            </a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item @if ('review' === $creating) active @endif"
                href="{{ route('capers.create') }}/review">
                Review
            </a></li>
            <li><a class="dropdown-item"
                href="{{ route('capers.create') }}/save">
                Save for later
            </a></li>
        </ul>
    </li>
</x-slot>
