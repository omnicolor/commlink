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
            <li><a class="dropdown-item @if ('rules' === $currentStep) active @endif"
                href="/characters/shadowrun5e/create/rules">
                Rules
            </a></li>
            <li><a class="dropdown-item @if ('priorities' === $currentStep) active @endif"
                href="/characters/shadowrun5e/create/priorities">
                Priorities
            </a></li>
            <li><a class="dropdown-item @if ('vitals' === $currentStep) active @endif"
                href="/characters/shadowrun5e/create/vitals">
                Vitals
            </a></li>
            <li><a class="dropdown-item @if ('attributes' === $currentStep) active @endif"
                href="/characters/shadowrun5e/create/attributes">
                Attributes
            </a></li>
            <li><a class="dropdown-item @if ('qualities' === $currentStep) active @endif"
                href="/characters/shadowrun5e/create/qualities">
                Qualities
            </a></li>
            @if (isset($character->priorities, $character->priorities['rulebooks']))
            @php
                $selectedBooks = explode(',', $character->priorities['rulebooks']);
            @endphp
            @if (in_array('run-and-gun', $selectedBooks, true))
            <li><a class="dropdown-item @if ('martial-arts' === $currentStep) active @endif"
                href="/characters/shadowrun5e/create/martial-arts">
                Martial Arts
            </a></li>
            @endif
            @endif
            <li><a class="dropdown-item @if ('skills' === $currentStep) active @endif"
                href="/characters/shadowrun5e/create/skills">
                Skills
            </a></li>
            <li><a class="dropdown-item @if ('knowledge' === $currentStep) active @endif"
                href="/characters/shadowrun5e/create/knowledge">
                Knowledge skills
            </a></li>
            @if ($character->isMagicallyActive())
            <li><a class="dropdown-item @if ('magic' === $currentStep) active @endif"
                href="/characters/shadowrun5e/create/magic">
                Magic
            </a></li>
            @endif
            @if ($character->isTechnomancer())
            <li><a class="dropdown-item @if ('resonance' === $currentStep) active @endif"
                href="/characters/shadowrun5e/create/resonance">
                Resonance
            </a></li>
            @endif
            <li><a class="dropdown-item @if ('augmentations' === $currentStep) active @endif"
                href="/characters/shadowrun5e/create/augmentations">
                Augmentations
            </a></li>
            <li><a class="dropdown-item @if ('weapons' === $currentStep) active @endif"
                href="/characters/shadowrun5e/create/weapons">
                Weapons
            </a></li>
            <li><a class="dropdown-item @if ('armor' === $currentStep) active @endif"
                href="/characters/shadowrun5e/create/armor">
                Armor
            </a></li>
            <li><a class="dropdown-item @if ('gear' === $currentStep) active @endif"
                href="/characters/shadowrun5e/create/gear">
                Gear
            </a></li>
            <li><a class="dropdown-item @if ('vehicles' === $currentStep) active @endif"
                href="/characters/shadowrun5e/create/vehicles">
                Vehicles
            </a></li>
            <li><a class="dropdown-item @if ('social' === $currentStep) active @endif"
                href="/characters/shadowrun5e/create/social">
                Social
            </a></li>
            <li><a class="dropdown-item @if ('background' === $currentStep) active @endif"
                href="/characters/shadowrun5e/create/background">
                Background
            </a></li>

            <li><hr class="dropdown-divider"></li>

            <li><a class="dropdown-item @if ('review' === $currentStep) active @endif"
                href="/characters/shadowrun5e/create/review">
                Review
            </a></li>
        </ul>
    </li>
</x-slot>
