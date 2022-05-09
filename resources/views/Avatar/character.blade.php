<x-app>
    <x-slot name="title">{{ $character }}</x-slot>
    <x-slot name="head">
        <style>
        </style>
    </x-slot>

    <div class="row my-4">
        <div class="col">{{ $character }}</div>
    </div>

    <x-slot name="javascript">
        <script>
        </script>
    </x-slot>
</x-app>
