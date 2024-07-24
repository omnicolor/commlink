<x-app>
    <x-slot name="title">Create character: Career</x-slot>
    @include('alien::create-navigation')

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
            <form action="{{ route('alien.save-career') }}" method="POST">
                @csrf

                <h1>Pick a name</h1>

                <div>
                    <label for="name" class="form-label">Name</label>
                    <input class="form-control" id="name" name="name" required
                        type="text" value="{{ $name }}">
                </div>

                <h1>Choose career</h1>
                <p>
                    The first choice for your character in Campaign play is your
                    career. Your career choice determines your background and
                    your role in the group. It influences your attributes, your
                    skills, your starting gear and what starting talent you can
                    have. There are nine core careers to choose from.
                </p>

                <div class="accordion" id="careers-list">
                    @foreach ($careers as $career)
                    <h2 class="accordion-header" id="heading-{{ $career->id }}">
                        <button aria-controls="collapse-{{ $career->id }}"
                            aria-expanded="{{ $character->career?->id === $career->id && (null === $character->career && !$loop->first) ? 'true' : 'false' }}"
                            class="accordion-button
                            @if ($character->career?->id !== $career->id && (null === $character->career && !$loop->first)) collapsed @endif
                            "
                            data-bs-target="#collapse-{{ $career->id }}"
                            data-bs-toggle="collapse" type="button">
                            {{ $career }}
                        </button>
                    </h2>
                    <div aria-labelledby="heading-{{ $career->id }}"
                        class="accordion-collapse collapse
                        @if ($character->career?->id === $career->id || (null === $character->career && $loop->first)) show @endif
                        "
                        data-bs-parent="#careers-list"
                        id="collapse-{{ $career->id }}">
                        <div class="accordion-body">
                            <p><small class="fs-6 text-muted">
                                {{ ucfirst($career->ruleset) }} p{{ $career->page }}
                            </small></p>
                            @can ('view data')
                            <p>{{ $career->description }}</p>
                            @endcan

                            <p>
                                <strong>Key attribute:</strong> {{ $career->keyAttribute }}<br>
                                <strong>Key skills:</strong> {{ implode(', ', $career->keySkills) }}<br>
                                <strong>Career talents:</strong> {{ implode(', ', $career->talents) }}
                            </p>

                            <button class="btn btn-primary" name="career"
                                type="submit" value="{{ $career->id }}">
                                @if ($character->career?->id !== $career->id)
                                Become a {{ $career }}
                                @else
                                Remain a {{ $career }}
                                @endif
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </form>
        </div>
        <div class="col-1"></div>
    </div>
</x-app>
