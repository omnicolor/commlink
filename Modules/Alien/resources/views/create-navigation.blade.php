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
            <li><a class="dropdown-item @if ('career' === $creating) active @endif"
                href="{{ route('alien.create', 'career') }}">
                Career
            </a></li>
            <li><a class="dropdown-item @if ('attributes' === $creating) active @endif"
                href="{{ route('alien.create', 'attributes') }}">
                Attributes
            </a></li>
            <li><a class="dropdown-item @if ('skills' === $creating) active @endif"
                href="{{ route('alien.create', 'skills') }}">
                Skills
            </a></li>
            <li><a class="dropdown-item @if ('talent' === $creating) active @endif"
                href="{{ route('alien.create', 'talent') }}">
                Talent
            </a></li>
            <li><a class="dropdown-item @if ('gear' === $creating) active @endif"
                href="{{ route('alien.create', 'gear') }}">
                Gear
            </a></li>
            <li><a class="dropdown-item @if ('finish' === $creating) active @endif"
                href="{{ route('alien.create', 'finish') }}">
                Finishing touches
            </a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item @if ('review' === $creating) active @endif"
                href="{{ route('alien.create', 'review') }}">
                Review
            </a></li>
            <li><a class="dropdown-item"
                href="{{ route('alien.create', 'save-for-later') }}">
                Save for later
            </a></li>
        </ul>
    </li>
</x-slot>
