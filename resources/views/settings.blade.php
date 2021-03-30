<x-app>
    <x-slot name="title">
        Settings
    </x-slot>

    @if(session('success'))
    <div class="alert alert-success mt-4">
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger mt-4">
        There were problems saving your settings.
    </div>
    @endif

    <div class="row mt-4">
        <div class="col">
            <div class="row">
                <div class="col">
                    <h1>Linked Slack Teams</h1>
                    <ul class="list-group">
                        @forelse ($user->slackLinks as $link)
                        <li class="list-group-item">
                            {{ $link->team_name }} ({{ $link->slack_team }}) —
                            {{ $link->user_name }} ({{ $link->slack_user }})
                        </li>
                        @empty
                        <li class="list-group-item">
                            You don't have any linked Slack teams!
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <form action="/settings/link-slack" method="POST">
                @csrf
                <div class="row mt-4">
                    <div class="col">
                        <h2>Link New Team</h2>
                        <p>
                            Linking a Slack Team will allow you to use Commlink
                            resources (like your characters) in Slack channels.
                            In addition, you'll be able to interact with your
                            character sheets in Commlink and have results show
                            up in your Slack channels.
                        </p>
                        <p>
                            <strong>Step 1.</strong>
                            Enter your Team ID (representing the Slack server)
                            and your User ID here. You can get them from
                            Commlink's Slack bot by typing <code>/roll
                            info</code> in any channel on the Slack team you
                            want to link. We don't ask for the Channel ID since
                            you may want to play different games in different
                            channels.
                        </p>
                        <p>
                            <strong>Step 2.</strong>
                            In a Slack channel to want to play a character in,
                            type <code>/roll link <character ID></code>, where
                            <code>character ID</code> is the ID you can
                            retrieve from your character sheet.
                        </p>
                    </div>
                </div>
                <div class="form-row mt-1">
                    <label class="col-4 col-form-label" for="slack-team">
                        Team ID
                    </label>
                    <div class="col">
                        <input autocomplete="off"
                            class="form-control @error('slack-team') is-invalid @enderror"
                            id="slack-team" name="slack-team" required
                            type="text" value="{{ old('slack-team') }}">
                        @error('slack-team')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="form-row mt-1">
                    <label class="col-4 col-form-label" for="slack-user">
                        User ID
                    </label>
                    <div class="col">
                        <input autocomplete="off"
                            class="form-control @error('slack-user') is-invalid @enderror"
                            id="slack-user" name="slack-user"
                            type="text">
                        @error('slack-user')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="form-row mt-1">
                    <button class="btn btn-primary" type="submit">
                        Link Team
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app>
