@php
use App\Models\Stillfleet\Role;
$chosenRole = new App\Models\Stillfleet\Role('banshee', 1);
//$chosenRole = null;
@endphp
<x-app>
    <x-slot name="title">Create character: Class</x-slot>

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
                In Stillfleet, your class is both an abstraction—a list of cool
                class powers that you can use within the game— and a specific
                profession within the Co.—a list of responsibilities you take on
                every time you start a venture.
            </p>

            <div class="row">
                <div class="col">
                    <div class="mb-3">
                    <label class="form-label" for="role">Class</label>
                    <select class="form-input" id="role" name="role">
                        <option value="">Select class
                    @foreach ($roles as $role)
                        <option @if ($chosenRole?->id === $role->id)
                            selected
                        @endif value="{{ $role->id }}">{{ $role }}
                    @endforeach
                    </select>
                    </div>

                    <div class="mb-3">
                    <label class="form-label" for="optional-power">Optional power</label>
                    <select class="form-input"
                        @if (null === $character->role) disabled @endif
                        id="optional-power" name="optional-power">
                        <option value="">Select power
                        @foreach ($chosenRole?->power_optional ?? [] as $power)
                            <option value="{{ $power->id }}">{{ $power }}
                        @endforeach
                    </select>
                    </div>
                </div>
                <div class="col" id="roles">
                @foreach ($roles as $role)
                    <div id="role-{{ $role->id }}" @if ($chosenRole?->id !== $role->id) class="d-none" @endif>
                    <h2>
                        {{ $role }}
                        <small class="fs-6 text-muted">{{ ucfirst($role->ruleset) }} p{{ $role->page }}</small>
                    </h2>
                    <p><strong>Responsibilities:</strong></p>
                    <ul>
                    @foreach ($role->responsibilities as $responsibility)
                        <li>{{ $responsibility }}</li>
                    @endforeach
                    </ul>
                    <p><strong>GRT:</strong> {{ implode(' + ', $role->grit) }}</p>
                    <p><strong>Advanced power list:</strong> {{ implode(', ', $role->power_advanced) }}</p>
                    <p>
                        <strong>Optional powers:</strong>
                        @foreach ($role->powers_optional as $power)
                            <span class="optional-power-{{ $power->id }}">{{ $power }}</span>@if (!$loop->last),@endif
                        @endforeach
                    </p>
                    <p>{{ $role->description }}</p>
                @endforeach
                </div>
            </div>
        </div>
        <div class="col-1"></div>
    </div>

    <x-slot name="javascript">
        <script>
            $(function () {
                'use strict';
                const powers = {
                    @foreach (Role::all() as $role)
                        '{{ $role->id }}': [
                        @foreach ($role->powers_optional as $power)
                            {id: '{{ $power->id }}', name: '{{ $power }}'}
                            @if (!$loop->last) , @endif
                        @endforeach
                        ]@if (!$loop->last) , @endif
                    @endforeach
                };

                $('#role').on('change', function (event) {
                    $('#roles div').addClass('d-none');
                    const selected = $(event.target).val();
                    if ('' === selected) {
                        $('#optional-power option').prop('selected', false);
                        $('#optional-power option').first().prop('selected', true);
                        $('#optional-power').prop('disabled', true);
                        return;
                    }
                    $('#role-' + selected).removeClass('d-none');
                    let options = '<option value="">Select power';
                    $.each(powers[selected], function (unused, power) {
                        options += '<option value="' + power.id + '">' + power.name;
                    });
                    $('#optional-power').html(options).prop('disabled', false);
                });
            });
        </script>
    </x-slot>
</x-app>
