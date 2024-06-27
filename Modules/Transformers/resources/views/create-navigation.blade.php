<x-slot name="navbar">
    <li class="nav-item">
        <a class="nav-link" href="/dashboard">Home</a>
    </li>
    <li class="nav-item dropdown">
        <a class="active nav-link dropdown-toggle" href="#"
            id="creating-dropdown" role="button" data-bs-toggle="dropdown"
            aria-expanded="false">
            Creating
            @if ($character->name)
                &ldquo;{{ $character->name }}&rdquo;
            @else
                new transformer
            @endif
        </a>
        <ul class="dropdown-menu" aria-labelledby="creating-dropdown">
            <li><a class="dropdown-item @if ('base' === $creating) active @endif"
                href="/characters/transformers/create/base">
                Basics
            </a></li>
            <li><a class="dropdown-item @if ('statistics' === $creating) active @endif"
                href="{{ route('transformers.create') }}/statistics">
                Statistics
            </a></li>
            <li><a class="dropdown-item @if ('function' === $creating) active @endif"
                href="{{ route('transformers.create') }}/function">
                Function
            </a></li>
            <li><a class="dropdown-item @if ('alt-mode' === $creating) active @endif"
                href="{{ route('transformers.create') }}/alt-mode">
                Alt.Mode
            </a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item"
                href="{{ route('transformers.create') }}/save-for-later">
                Save for later
            </a></li>
        </ul>
    </li>
</x-slot>
