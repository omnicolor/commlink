<x-slot name="navbar">
    <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard') }}">Home</a>
    </li>
    <li class="nav-item dropdown">
        <a class="active nav-link dropdown-toggle" href="#" id="creating-dropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Creating
            @if ($character->handle)
                &ldquo;{{ $character->handle }}&rdquo;
            @else
                new character
            @endif
        </a>
        <ul class="dropdown-menu" aria-labelledby="creating-dropdown">
            <li><a class="dropdown-item @if ('handle' === $creating) active @endif"
                href="/characters/cyberpunkred/create/handle">
                Handle
            </a></li>
            <li><a class="dropdown-item @if ('role' === $creating) active @endif"
                href="/characters/cyberpunkred/create/role">
                Role
            </a></li>
            <li><a class="dropdown-item @if ('lifepath' === $creating) active @endif"
                href="/characters/cyberpunkred/create/lifepath">
                Lifepath
            </a></li>
            <li><a class="dropdown-item @if ('stats' === $creating) active @endif"
                href="/characters/cyberpunkred/create/stats">
                Statistics
            </a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item @if ('review' === $creating) active @endif"
                href="/characters/cyberpunkred/create/review">
                Review
            </a></li>
            <li><a class="dropdown-item"
                href="/characters/cyberpunkred/create/save">
                Save for later
            </a></li>
        </ul>
    </li>
</x-slot>
