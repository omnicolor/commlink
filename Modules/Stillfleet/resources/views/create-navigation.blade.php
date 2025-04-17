@php
    if (!isset($creating)) {
        $creating = null;
    }
@endphp
<x-slot name="navbar">
    <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard') }}">Home</a>
    </li>
    <li class="nav-item dropdown">
        <a aria-expanded="false" class="active nav-link dropdown-toggle"
           data-bs-toggle="dropdown" href="#" id="creating-dropdown"
           role="button">
            Creating
            @if ($character->handle)
                &ldquo;{{ $character->handle }}&rdquo;
            @else
                new character
            @endif
        </a>
        <ul class="dropdown-menu" aria-labelledby="creating-dropdown">
            <li>
                <a class="dropdown-item @if ('class' === $creating) active @endif"
                    href="{{ route('stillfleet.create', 'class') }}">
                    Class
                </a>
            </li>
            <li>
                <a class="dropdown-item @if ('class-powers' === $creating) active @endif"
                    href="{{ route('stillfleet.create', 'class-powers') }}">
                    Class powers
                </a>
            </li>
            <li>
                <a class="dropdown-item @if ('species' === $creating) active @endif"
                    href="{{ route('stillfleet.create', 'species') }}">
                    Species
                </a>
            </li>
            <li>
                <a class="dropdown-item @if ('species-powers' === $creating) active @endif"
                    href="{{ route('stillfleet.create', 'species-powers') }}">
                    Species powers
                </a>
            </li>
            <li>
                <a class="dropdown-item @if ('attributes' === $creating) active @endif"
                    href="{{ route('stillfleet.create', 'attributes') }}">
                    Attributes
                </a>
            </li>
            <li>
                <a class="dropdown-item @if ('gear' === $creating) active @endif"
                   href="{{ route('stillfleet.create', 'gear') }}">
                    Gear
                </a>
            </li>

            <li>
                <hr class="dropdown-divider">
            </li>

            <li>
                <a class="dropdown-item" href="#">Save for later</a>
            </li>
            <li>
                <a class="dropdown-item @if ('review' === $creating) active @endif" href="#">
                    Review
                </a>
            </li>
        </ul>
    </li>
</x-slot>
