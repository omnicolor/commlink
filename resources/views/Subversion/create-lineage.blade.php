<x-app>
    <x-slot name="title">Create character: Name and lineage</x-slot>
    <x-slot name="head">
        <style>
            .lineage-options {
                list-style: none;
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
            <div class="col-4"></div>
        </div>
    @endif

    <form action="" id="form" method="POST"
        @if ($errors->any()) class="was-validated" @endif
        novalidate>
    @csrf

    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col"><h1>Name</h1></div>
        <div class="col-4"></div>
    </div>

    <div class="row">
        <div class="col-1"></div>
        <div class="col-1">
            <label class="form-label" for="name">Name</label>
        </div>
        <div class="col">
            <input class="form-control" id="name" name="name" required
                type="text" value="{{ old('name') ?? $character->name ?? '' }}">
        </div>
        <div class="col-4"></div>
    </div>

    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col"><h1>Lineage</h1></div>
        <div class="col-4"></div>
    </div>

    <div class="row">
        <div class="col-1"></div>
        <div class="col">
            Each character chooses one lineage and one lineage option. The
            playable lineages in Subversion represent the most common biological
            peoples on the planet. Choosing a lineage (Dwarf, Elf, Goblin,
            Harmaku, Human, Orc, or Yettin) impacts your PC’s general physical
            appearance and abilities, and allows you to choose from a menu of
            options reflecting the variety of people who share that lineage.
        </div>
        <div class="col-4"></div>
    </div>

    @foreach ($lineages as $lineage)
    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col">
            <h3>
                <input class="form-check-input" name="lineage"
                    @if ($lineageId === $lineage->id) checked @endif
                    id="lineage-{{ $lineage->id }}" required type="radio"
                    value="{{ $lineage->id }}">
                {{ $lineage }}
            </h3>
        </div>
        <div class="col-4"></div>
    </div>
    @can('view data')
    <div class="row">
        <div class="col-1"></div>
        <div class="col">{{ $lineage->description }}</div>
        <div class="col-4"></div>
    </div>
    @endcan
    <div class="row">
        <div class="col-1"></div>
        <div class="col">
            Choose 1 from the list below:
            <ul class="lineage-options">
            @foreach ($lineage->options as $option)
                <li>
                    <label class="form-label">
                        <input class="form-check-input"
                            @if ($lineageOptionId === $option->id) checked @endif
                            id="{{ $lineage->id }}-{{ $option->id }}"
                            name="option" type="radio"
                            value="{{ $option->id }}">
                        <strong>{{ $option }}:</strong>
                        {{ $option->description }}
                    </label>
                </li>
            @endforeach
        </div>
        <div class="col-4"></div>
    </div>
    @endforeach

    </form>

    @include('Subversion.create-fortune')

    <x-slot name="javascript">
        <script>
			(function () {
				'use strict';
            })();
        </script>
    </x-slot>
</x-app>
