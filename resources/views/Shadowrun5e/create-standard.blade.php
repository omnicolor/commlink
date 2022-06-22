<x-app>
    <x-slot name="title">Create character</x-slot>
    <x-slot name="head">
        <link href="/css/Shadowrun5e/character-generation.css" rel="stylesheet">
    </x-slot>

    @include('Shadowrun5e.create-navigation')

    @if ($errors->any())
        <div class="alert alert-danger mt-4">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('shadowrun5e.create-standard') }}" method="post">
    @csrf

    <div class="row">
        <div class="col-1"></div>
        <div class="col">
            <h1>Standard priority</h1>
        </div>
    </div>

    <div class="row my-4">
        <div class="col-1"></div>
        <div class="col">
            The Priority System is a table with the following columns: Metatype,
            Attributes, Magic or Resonance, Skills, and Resources. The rows are
            divided into Priority Levels ranging from Priority A to Priority E.
            Players assign a specific Priority Level for each of the columns to
            their character depending on their preferences. The values assigned
            must be different for each column (representing each aspect of the
            character), and there can be no duplications. For example, a
            character may not have Priority Level B for Magic or Resonance and
            Priority B for Resources. The higher the Priority Level (A, B,
            etc.), the more valuable it is for the character. Characters use
            Karma later on to customize their characters even further.
        </div>
        <div class="col-3"></div>
    </div>

    @foreach ($priorities as $priority => $choices)
    <div class="row my-1">
        <div class="col-1"></div>
        <div class="col-1">
            {{ strtoupper($priority) }}
        </div>
        <div class="col">
            <select class="priority form-control" id="priority-{{ $priority }}"
                name="priority-{{ $priority }}">
                <option value="">&hellip;</option>
                @foreach ($choices as $choice)
                <option
                    @if (isset($selected[$priority]) && $selected[$priority] === $choice['value'])
                        selected
                    @endif
                    title="{{ $choice['title'] }}"
                    value="{{ $choice['value'] }}">
                    {!! $choice['name'] !!}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col" id="extra-{{ $priority }}">
            @if ('metatype' === $selected[$priority])
            <select class="form-control" id="metatype" name="metatype">
                <option value="">&hellip;</option>
                @foreach ($races[$priority] as $race => $description)
                <option
                    @if ($selected['metatype'] === $race)
                        selected
                    @endif
                    value="{{ $race }}">{{ $description }}</option>
                @endforeach
            </select>
            @elseif ('magic' === $selected[$priority] && 'e' !== $priority)
            <select class="form-control" id="magic" name="magic">
                <option value="">&hellip;</option>
                @foreach ($magic[$priority] as $key => $value)
                <option
                    @if (isset($selected['magic']) && $selected['magic'] === $key)
                        selected
                    @endif
                    title="{{ $value['description'] }}"
                    value="{{ $key }}">{{ $value['name'] }}</option>
                @endforeach
            </select>
            @else
            &nbsp;
            @endif
        </div>
        <div class="col-3"></div>
    </div>
    @endforeach

    @include('Shadowrun5e.create-next')
    </form>

    @include('Shadowrun5e.create-points')

    <x-slot name="javascript">
        <script>
            const gameplay = '{{ $gameplay }}';
            let character = @json($character);
        </script>
        <script src="/js/Shadowrun5e/Points.js"></script>
        <script src="/js/Shadowrun5e/create-common.js"></script>
        <script src="/js/Shadowrun5e/create-standard-priority.js"></script>
    </x-slot>
</x-app>
