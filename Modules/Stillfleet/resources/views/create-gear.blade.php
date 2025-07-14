<x-app>
    <x-slot name="title">Create character: Class</x-slot>

    @include('stillfleet::create-navigation')

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
            <h1>Gear</h1>

            <p>You have {{ $money }} voidguilder to start with.</p>
        </div>
        <div class="col-1"></div>
    </div>
</x-app>
