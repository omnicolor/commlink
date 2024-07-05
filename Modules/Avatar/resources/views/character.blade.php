<x-app>
    <x-slot name="title">{{ $character }}</x-slot>
    <x-slot name="head">
        <style>
        </style>
    </x-slot>
    <x-slot name="navbar">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">Home</a>
        </li>
        <li class="nav-item">
            <span class="nav-link active">{{ $character }}</span>
        </li>
    </x-slot>

    <div class="row my-4">
        <div class="col">{{ $character }}</div>
    </div>

    <x-slot name="javascript">
        <script>
        </script>
    </x-slot>
</x-app>
