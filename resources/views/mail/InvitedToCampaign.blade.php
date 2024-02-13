<!doctype html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <title>{{ config('app.name') }} - Invited to campaign</title>
    <link rel="stylesheet" href="{{ config('app.url') }}/css/bootstrap.min.css">
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand active" href="{{ config('app.url') }}">{{ config('app.name') }}</a>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-3"></div>
        <div class="col">
            <h1 class="mb-4">You've been invited to do some role playing!</h1>

            <p>{{ $name }},</p>

            <p>
                The gamemaster, {{ $invitor->name }}, for
                &ldquo;{{ $campaign->name }}&rdquo; (a campaign playing {{ $system }})
                has sent you an invitation to join their virtual table using
                {{ config('app.name') }}!
            </p>

            <p>
                Hopefully, you're interested in role playing and this invitation
                was not sent in error. There's a button below that will allow
                you to mark this as spam if you have no idea what any of this
                means, and we'll revoke {{ $invitor->name }}'s ability to send
                out invitations.
            </p>

            <p>
                If you <strong>are</strong> interested in role playing games,
                great! Hopefully {{ $invitor->name }} has told you a bit about
                {{ config('app.name') }}, but in case they haven't:
                {{ config('app.name') }} is a tool for playing role playing
                games online, and managing all of the complexity of modern RPGs
                and the campaigns that bring them to life. You can create a new
                character, or import an existing one, and link it to the
                campaign. Your gamemaster will be able to see your character
                sheets on a their virtual GM screen, and it'll handle things
                like session rewards, damage, and initative automatically.
            </p>

            <p>
                In addition, {{ config('app.name') }} has a dice roller
                integrated with popular chat platforms like Discord and Slack.
                Linking your character to your chat's channel and user will
                allow you to roll dice in the channel as your character.
            </p>

            <div class="row">
                <div class="col">
                    <a class="btn btn-primary btn-lg" href="{{ $accept_url }}">
                        Accept
                    </a>
                </div>
                <div class="col">
                    <a class="btn btn-secondary btn-lg" href="{{ $decline_url }}">
                        Decline
                    </a>
                </div>
                <div class="col">
                    <a class="btn btn-danger btn-lg" href="{{ $spam_url }}">
                        Spam
                    </a>
                </div>
            </div>
        </div>
        <div class="col-lg-3"></div>
    </div>
</div>
</body>
</html>
