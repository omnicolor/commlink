<x-app>
    <x-slot name="title">Campaign: {{ $campaign }}</x-slot>

    <div class="card" style="width: 18rem;">
        <div class="card-header">
            <h5 class="card-title">Initiative</h5>
            <h6 class="card-subtitle mb-2 text-muted">Waiting...</h6>
        </div>
        <ul class="list-group list-group-flush" id="combatants">
            @forelse ($initiative as $combatant)
            <li class="list-group-item" id="init-{{ \Str::of($combatant)->replaceMatches('/^[0-9]*/', '')->replaceMatches('/([^A-Za-z0-9[\]{}_.:-])\s?/', '')->lower() }}">
                {{ $combatant }}
                <span class="float-end">
                    <span class="score">{{ $combatant->initiative }}</span>
                    <i class="bi bi-three-dots-vertical"></i>
                </span>
            </li>
            @empty
            <li class="list-group-item" id="no-combatants">
                No combatants.
            </li>
            @endforelse
        </ul>
        <div class="card-footer">
            <i class="bi bi-stop-circle"></i>
            <i class="bi bi-play-circle"></i>
            <i class="bi bi-skip-forward-circle"></i>
            <i class="bi bi-plus-circle"></i>
            <i class="bi bi-x-circle"></i>
        </div>
    </div>

    <x-slot name="javascript">
        <script>
            /**
             * Take a character's name, and return it ready to be a unique
             * element id.
             * @param {string} string
             * @return {string}
             */
            function makeSafeForId(string) {
                return string.replace(/^[0-9]*/, '')
                    .replace(/([^A-Za-z0-9[\]{}_.:-])\s?/g, '')
                    .toLowerCase();
            }

            /**
             * Add a new combatant to the initiative list.
             * @param {string} name Combatant's name
             * @param {Number} initiative
             */
            function addInitiativeRow(name, initiative) {
                $('#combatants').append(
                    '<li class="list-group-item" id="init-'
                        + makeSafeForId(name) + '">' + name
                        + '<span class="float-end"><span class="score">'
                        + initiative + '</span>'
                        + '<i class="bi bi-three-dots-vertical"></i>'
                        + '</span></li>'
                );
            }

            /**
             * Update an existing combatant's initiative.
             * @param {Jquery} el Element containing the combatant's old init
             * @param {Number} initiative
             */
            function updateInitiative(el, initiative) {
                el.find('.score').html(initiative);
            }

            /**
             * Compare two initiative HTML rows.
             * @param {Element} a
             * @param {Element} b
             * @return {Number}
             */
            function compareInitiatives(a, b) {
                const aVal = parseInt($(a).find('.score').text(), 10);
                const bVal = parseInt($(b).find('.score').text(), 10);
                return bVal - aVal;
            }

            /**
             * Sort the initiative listing on the page.
             */
            function sortInitiatives() {
                const combatantsEl = $('#combatants');
                let rows = combatantsEl.children()
                    .toArray()
                    .sort(compareInitiatives);
                combatantsEl.html('');
                $.each(rows, function (unused, row) {
                    combatantsEl.append(row);
                });
            }

            Echo.private(`campaign.{{ $campaign->id }}`)
                .listen('InitiativeAdded', (e) => {
                    $('#no-combatants').hide();
                    const el = $('#init-' + makeSafeForId(e.name));
                    if (0 === el.length) {
                        addInitiativeRow(e.name, e.initiative.initiative);
                    } else {
                        updateInitiative(el, e.initiative.initiative);
                    }
                    sortInitiatives();
                });
        </script>
    </x-slot>
</x-app>
