<x-slot name="navbar">
    <li class="nav-item">
        <a class="nav-link" href="/dashboard">Home</a>
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
            <li><a class="dropdown-item @if ('lineage' === $creating) active @endif"
                href="{{ route('subversion.create', 'lineage') }}">
                Name and lineage
            </a></li>
            <li><a class="dropdown-item @if ('origin' === $creating) active @endif"
                href="{{ route('subversion.create', 'origin') }}">
                Origin
            </a></li>
            <li><a class="dropdown-item @if ('background' === $creating) active @endif"
                href="{{ route('subversion.create', 'background') }}">
                Background
            </a></li>
            <li><a class="dropdown-item @if ('caste' === $creating) active @endif"
                href="{{ route('subversion.create', 'caste') }}">
                Caste
            </a></li>
            <li><a class="dropdown-item @if ('ideology' === $creating) active @endif"
                href="{{ route('subversion.create', 'ideology') }}">
                Ideology
            </a></li>
            <li><a class="dropdown-item @if ('values' === $creating) active @endif"
                href="{{ route('subversion.create', 'values') }}">
                Values
            </a></li>
            <li><a class="dropdown-item @if ('impulse' === $creating) active @endif"
                href="{{ route('subversion.create', 'impulse') }}">
                Impulse
            </a></li>
            <li><a class="dropdown-item @if ('hooks' === $creating) active @endif"
                href="{{ route('subversion.create', 'hooks') }}">
                Dramatic hooks
            </a></li>
            <li><a class="dropdown-item @if ('relations' === $creating) active @endif"
                href="#">
                Relations
            </a></li>
            <li><a class="dropdown-item @if ('debt' === $creating) active @endif"
                href="#">
                Debt
            </a></li>
            <li><a class="dropdown-item @if ('attributes' === $creating) active @endif"
                href="#">
                Attributes
            </a></li>
            <li><a class="dropdown-item @if ('skills' === $creating) active @endif"
                href="#">
                Skills
            </a></li>
            <li><a class="dropdown-item @if ('paradigms' === $creating) active @endif"
                href="#">
                Paradigms
            </a></li>
            <li><a class="dropdown-item @if ('gear' === $creating) active @endif"
                href="#">
                Gear
            </a></li>
            <li><a class="dropdown-item @if ('languages' === $creating) active @endif"
                href="#">
                Languages
            </a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item @if ('review' === $creating) active @endif"
                href="#">
                Review
            </a></li>
            <li><a class="dropdown-item" href="#">Save for later</a></li>
        </ul>
    </li>
</x-slot>
