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
        @forelse ($contacts as $contact)
            <tr data-bs-toggle="tooltip" data-bs-placement="left"
                title="{{ $contact->notes }}">
                <td>{{ $contact->name }}</td>
                <td>{{ $contact->archetype }}</td>
                <td>{{ $contact->loyalty ?: '?' }}</td>
                <td>{{ $contact->connection ?: '?' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4">
                    <span class="badge rounded-pill bg-danger ms-2">!</span>
                    Character does not know anyone. In the shadows, you are who
                    you know. Meet some valuable contacts on the
                    <a href="/characters/shadowrun5e/create/contacts">contacts page</a>.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
@endif
