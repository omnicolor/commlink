<div>
    <h2>Metadata</h2>

    <div class="row">
        <div class="col-3">Era</div>
        <div class="col">{{ $campaign->options['era'] }}</div>
    </div>
    <div class="row">
        <div class="col-3">Scope</div>
        <div class="col">{{ $campaign->options['scope'] }}</div>
    </div>
    <div class="row">
        <div class="col-3">Focus</div>
        <div class="col">
            to <strong>{{ $campaign->options['focus'] }}</strong>
            {{ $campaign->options['focusObject'] }}
        </div>
    </div>
    <div class="row">
        <div class="col-3">Focus details</div>
        <div class="col">{{ $campaign->options['focusDetails'] }}</div>
    </div>
</div>
