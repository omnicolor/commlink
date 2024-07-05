@if (0 !== count($contacts) || $charGen)
<div class="card" id="contacts">
    <div class="card-header">contacts</div>
    <table class="card-body table">
        <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Archetype</th>
                <th scope="col">Loyalty</th>
                <th scope="col">Connection</th>
            </tr>
        </thead>
        <tbody>
        @php
            $class = '';
        @endphp
        @forelse ($contacts as $contact)
            @php
                if ($loop->last) {
                    $class = 'class="border-bottom-0"';
                }
            @endphp
            <tr data-bs-toggle="tooltip" data-bs-placement="left"
                title="{{ $contact->notes }}">
                <td {!! $class !!}>{{ $contact->name }}</td>
                <td {!! $class !!}>{{ $contact->archetype }}</td>
                <td {!! $class !!}>{{ $contact->loyalty ?: '?' }}</td>
                <td {!! $class !!}>{{ $contact->connection ?: '?' }}</td>
            </tr>
        @empty
            <tr>
                <td class="border-bottom-0" colspan="4">
                    <span class="badge rounded-pill bg-danger ms-2">!</span>
                    Character does not know anyone. In the shadows, you are who
                    you know. Meet some valuable contacts on the
                    <a href="/characters/shadowrun5e/create/social">social page</a>.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
@endif
