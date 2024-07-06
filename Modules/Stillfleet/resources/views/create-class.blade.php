@php
use Modules\Stillfleet\Models\Role;
@endphp
<x-app>
    <x-slot name="title">Create character: Class</x-slot>
    <x-slot name="head">
        <style>
            .accordion-header,
            .accordion-collapse {
                border-bottom: var(--bs-accordion-border-width) solid var(--bs-accordion-border-color);
            }
            .accordion-header:has(+ .show) {
                border-bottom: 0;
            }
        </style>
    </x-slot>

    @if ($errors->any())
        <div class="my-4 row">
            <div class="col-1"></div>
            <div class="col">
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-1"></div>
        </div>
    @endif

    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col">
            <h1>Class</h1>
            <p>
                In Stillfleet, your class is both an abstraction— a list of cool
                class powers that you can use within the game— and a specific
                profession within the Co.— a list of responsibilities you take
                on every time you start a venture.
            </p>

            <form action="{{ route('stillfleet.create-class') }}" method="POST">
            @csrf
            <div class="accordion" id="roles-list">
                <div class="accordion-item">
                    @foreach ($roles as $role)
                    <h2 class="accordion-header" id="heading-{{ $role->id }}">
                        <button aria-controls="collapse-{{ $role->id }}"
                            aria-expanded="{{ $chosenRole?->id === $role->id ? 'true' : 'false' }}"
                            class="accordion-button
                            @if ($chosenRole?->id !== $role->id) collapsed @endif
                            "
                            data-bs-target="#collapse-{{ $role->id }}"
                            data-bs-toggle="collapse" type="button">
                            {{ $role }}
                        </button>
                    </h2>
                    <div aria-labelledby="heading-{{ $role->id }}"
                        class="accordion-collapse collapse
                        @if ($chosenRole?->id === $role->id) show @endif
                        "
                        data-bs-parent="#roles-list"
                        id="collapse-{{ $role->id }}">
                        <div class="accordion-body">
                            <p><small class="fs-6 text-muted">
                                {{ ucfirst($role->ruleset) }} p{{ $role->page }}
                            </small></p>
                            @can ('view data')
                            <p>{{ $role->description }}</p>
                            @endcan
                            <p><strong>Responsibilities:</strong></p>
                            <ul>
                            @foreach ($role->responsibilities as $responsibility)
                                <li>{{ $responsibility }}</li>
                            @endforeach
                            </ul>

                            <p>
                                <strong>GRT:</strong>
                                {{ implode(' + ', $role->grit) }}
                            </p>

                            <p>
                                <strong>Advanced power list:</strong>
                                {{ implode(', ', $role->power_advanced) }}
                            </p>

                            <p>
                                <strong>Marquee power:</strong>
                                {{ $role->power_marquee }}
                                @can ('view data')
                                    &mdash; {{ $role->power_marquee->description }}
                                @endcan
                            </p>

                            <p><strong>Other class powers:</strong></p>
                            <ul>
                            @foreach ($role->powers_other as $power)
                                <li>
                                    {{ $power }}
                                    @can ('view data')
                                        &mdash; {{ $power->description }}
                                    @endcan
                                </li>
                            @endforeach
                            </ul>

                            <p><strong>Optional powers:</strong> (chosen on the next page)</p>
                            <ul>
                            @foreach ($role->powers_optional as $power)
                                <li>
                                    {{ $power }}
                                    @can ('view data')
                                        &mdash; {{ $power->description }}
                                    @endcan
                                </li>
                            @endforeach
                            </ul>

                            <button class="btn btn-primary" name="role" type="submit" value="{{ $role->id }}">
                                Become a {{ $role }}
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            </form>
        </div>
        <div class="col-1"></div>
    </div>

    <x-slot name="javascript">
        <script>
            $(function () {
                'use strict';
            });
        </script>
    </x-slot>
</x-app>
