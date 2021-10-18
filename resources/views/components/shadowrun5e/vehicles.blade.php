@if (0 !== count($vehicles) || $charGen)
<div class="card" id="vehiclelist">
    <div class="card-header">vehicles</div>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Type</th>
                <th scope="col">H</th>
                <th scope="col">S</th>
                <th scope="col">A</th>
                <th scope="col">B</th>
                <th scope="col">Armor</th>
                <th scope="col">Pilot</th>
                <th scope="col">Sens</th>
                <th scope="col">Seats</th>
            </tr>
        </thead>
        <tbody>
        @php
            $class = '';
        @endphp
        @forelse ($vehicles as $vehicle)
            @php
                if ($loop->last) {
                    $class = 'class="border-bottom-0"';
                }
            @endphp
            <tr>
                <td {!! $class !!} data-bs-html="true" data-bs-toggle="tooltip"
                    data-bs-placement="right"
                    title="<p>{{ str_replace('||', '</p><p>', $vehicle->description) }}</p>">
                    {{ $vehicle }}
                    @if (isset($vehicle->subname))
                        {{ $vehicle->subname }}
                    @endif
                </td>
                <td {!! $class !!}>{{ $vehicle->type }}</td>
                <td {!! $class !!}>{{ $vehicle->handling }}</td>
                <td {!! $class !!}>{{ $vehicle->speed }}</td>
                <td {!! $class !!}>{{ $vehicle->acceleration }}</td>
                <td {!! $class !!}>{{ $vehicle->body }}</td>
                <td {!! $class !!}>{{ $vehicle->armor }}</td>
                <td {!! $class !!}>{{ $vehicle->pilot }}</td>
                <td {!! $class !!}>{{ $vehicle->sensor }}</td>
                <td {!! $class !!}>{{ $vehicle->seats }}</td>
            </tr>
        @empty
            <tr>
                <td class="border-bottom-0" colspan="10">
                    <span class="badge rounded-pill bg-warning">!</span>
                    Character has no vehicles. Unless you want to take public
                    transportation every where you go, buy one on the
                    <a href="/characters/shadowrun5e/create/vehicles">vehicles page</a>.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
@endif
