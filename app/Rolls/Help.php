<?php

declare(strict_types=1);

namespace App\Rolls;

use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;

class Help extends Roll
{
    /**
     * @var array<int, array<string, string>>
     */
    protected array $data = [];

    /**
     * Constructor.
     * @param string $content
     * @param string $character
     * @param Channel $channel
     */
    public function __construct(
        string $content,
        string $character,
        Channel $channel
    ) {
        parent::__construct($content, $character, $channel);
        $this->data[] = [
            'title' => sprintf('About %s', config('app.name')),
            'slackText' => \sprintf(
                '%1$s is a Slack bot that lets you roll dice appropriate for '
                    . 'various RPG systems. For example, if you are playing '
                    . 'The Expanse, it will roll three dice, marking one of '
                    . 'them as the "drama die", adding up the result with the '
                    . 'number you give for your attribute+focus score, and '
                    . 'return the result along with any stunt points.'
                    . \PHP_EOL . \PHP_EOL . 'If your game uses the web app for '
                    . '<%2$s|%1$s> as well, links in the app will '
                    . 'automatically roll in Slack, and changes made to your '
                    . 'character via Slack will appear in %1$s.',
                config('app.name'),
                config('app.url')
            ),
            'discordText' => \sprintf(
                '%1$s is a Discord bot that lets you roll dice appropriate for '
                    . 'various RPG systems. For example, if you are playing '
                    . 'The Expanse, it will roll three dice, marking one of '
                    . 'them as the "drama die", adding up the result with the '
                    . 'number you give for your attribute+focus score, and '
                    . 'return the result along with any stunt points.'
                    . \PHP_EOL . \PHP_EOL
                    . 'If your game uses the web app for %1$s (%2$s) as well, '
                    . 'links in the app will automatically roll in Discord, '
                    . 'and changes made to your character via Discord will '
                    . 'appear in %1$s.' . \PHP_EOL . \PHP_EOL,
                config('app.name'),
                config('app.url')
            ),
            'color' => TextAttachment::COLOR_INFO,
        ];

        if (null === $this->chatUser) {
            $this->addHelpForUnlinkedUser();
        }
        if (null === $this->channel->system) {
            $this->addHelpForUnlinkedChannel();
        }
    }

    /**
     * Return the roll formatted for Slack.
     * @return SlackResponse
     */
    public function forSlack(): SlackResponse
    {
        $response = new SlackResponse(channel: $this->channel);
        foreach ($this->data as $element) {
            $response->addAttachment(new TextAttachment(
                $element['title'],
                $element['slackText'] ?? $element['text'],
                $element['color'],
            ));
        }
        return $response;
    }

    /**
     * Return the roll formatted for Discord.
     * @return string
     */
    public function forDiscord(): string
    {
        $value = '';
        foreach ($this->data as $element) {
            $value .= \sprintf('**%s**', $element['title']) . \PHP_EOL
                . ($element['discordText'] ?? $element['text']) . \PHP_EOL;
        }
        return $value;
    }

    /**
     * Add help for user if they haven't linked their Commlink user yet.
     */
    protected function addHelpForUnlinkedUser(): void
    {
        $this->data[] = [
            'title' => 'Note for unregistered users:',
            'slackText' => \sprintf(
                'Your Slack user has not been linked with a %s user. '
                    . 'Go to the <%s/settings|settings page> and copy the '
                    . 'command listed there for this server. If the server '
                    . 'isn\'t listed, follow the instructions there to add '
                    . 'it. You\'ll need to know your server ID (`%s`) and '
                    . 'your user ID (`%s`).',
                config('app.name'),
                config('app.url'),
                $this->channel->server_id,
                $this->channel->user
            ),
            'discordText' => \sprintf(
                'Your Discord user has not been linked with a %s user. Go to '
                    . 'the settings page (%s/settings) and copy the command '
                    . 'listed there for this server. If the server isn\'t '
                    . 'listed, follow the instructions there to add it. '
                    . 'You\'ll need to know your server ID (`%s`) and your '
                    . 'user ID (`%s`).',
                config('app.name'),
                config('app.url'),
                $this->channel->server_id,
                $this->channel->user,
            ),
            'color' => TextAttachment::COLOR_DANGER,
        ];
    }

    /**
     * Add help for a channel that hasn't been registered.
     */
    protected function addHelpForUnlinkedChannel(): void
    {
        $systems = [];
        foreach (config('app.systems') as $code => $name) {
            $systems[] = \sprintf('%s (%s)', $code, $name);
        }
        $this->data[] = [
            'title' => 'Commands for unregistered channels:',
            'text' => '· `help` - Show help' . \PHP_EOL
                . '· `XdY[+C] [text]` - Roll X dice with Y sides, '
                . 'optionally adding C to the result, optionally '
                . 'describing that the roll is for "text"' . \PHP_EOL
                . '· `coin` - Flip a coin'
                . '· `register <system>` - Register this channel for '
                . 'system code <system>, where system is one of: '
                . implode(', ', $systems) . \PHP_EOL
                . $this->getCampaignsHelp(),
            'color' => TextAttachment::COLOR_INFO,
        ];
    }

    /**
     * Get additional help for a channel that has no campaign.
     * @return string
     */
    protected function getCampaignsHelp(): string
    {
        $user = optional($this->chatUser)->user;
        if (null === $user) {
            return '';
        }
        $campaigns = $user->campaignsRegistered->merge($user->campaignsGmed)
            ->unique();
        if (0 === count($campaigns)) {
            return '';
        }
        $campaignList = [];
        foreach ($campaigns as $campaign) {
            $campaignList[] = sprintf(
                '· %d - %s (%s)',
                $campaign->id,
                $campaign->name,
                $campaign->getSystem()
            );
        }
        return '· `campaign <campaignId>` - Register this channel for '
            . 'the campaign with ID <campaignId>. Your campaigns: '
            . implode(', ', $campaignList);
    }
}
