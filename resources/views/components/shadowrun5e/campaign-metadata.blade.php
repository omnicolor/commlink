<div>
    <h2>Metadata</h2>

    <div class="row">
        <div class="col-3">Start date</div>
        <div class="col">{{ $campaign->options['startDate'] }}</div>
    </div>
    @php
        $current = $campaign->options['currentDate'] ?? $campaign->options['startDate'];
        if (!is_null($current)) {
            $current = new \Carbon\CarbonImmutable($current);
        }
    @endphp
    <div class="row">
        <div class="col-3">Current date</div>
        <div class="col">
            {{ !is_null($current) ? $current->format('l, F jS Y') : 'no date set' }}
            <button type="button" class="btn btn-link btn-small" data-bs-toggle="modal" data-bs-target="#current-date">
                <i class="bi bi-calendar-date"></i>
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-3">Creation systems</div>
        <div class="col">{{ implode(', ', $campaign->options['creation']) }}</div>
    </div>
    @php
        $books = \App\Models\Shadowrun5e\Rulebook::all();
        $enabled = $campaign->options['rulesets'];
        foreach ($books as $key => $book) {
            if (!in_array($key, $enabled, true) && !$book->required) {
                // Book is not enabled.
                $books[$key] = '<strike>' . (string)$book . '</strike>';
                continue;
            }
            $books[$key] = (string)$book;
        }
    @endphp
    <div class="row">
        <div class="col-3">Rulesets</div>
        <div class="col"><small>{!! implode(', ', $books) !!}</small></div>
    </div>
    <div class="row">
        <div class="col-3">Weather</div>
        <div class="col">
    @if (array_key_exists('weather', $campaign->options) && !is_null($current))
        <a href="{{ $campaign->options['weather'] }}{{ $current->subYears(70)->format('Y-m-d') }}"
           target="_blank">Current weather</a>
    @else
        No weather set
    @endif
    </div>
</div>

<div aria-hidden="true" aria-labelledby="current-date-label" class="modal fade"
    id="current-date" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="current-date-label">Current date</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="update-current-date">Current date</label>
                @php
                    $dateShown = $current;
                    if (is_null($current)) {
                        $dateShown = (new \Carbon\CarbonImmutable())->addYears(61);
                    }
                @endphp
                <input id="update-current-date" type="date" value="{{ $dateShown->format('Y-m-d') }}">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
