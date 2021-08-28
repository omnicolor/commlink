<?php

declare(strict_types=1);

namespace App\Http\Responses\Slack;

use App\Exceptions\SlackException;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;

/**
 * Generic help response for Slack.
 */
class HelpResponse extends SlackResponse
{
    /**
     * Constructor.
     * @param string $content
     * @param int $status
     * @param array<string, string> $headers
     * @param ?Channel $channel
     */
    public function __construct(
        string $content = '',
        int $status = 200,
        array $headers = [],
        ?Channel $channel = null,
    ) {
        parent::__construct($content, $status, $headers, $channel);
        if (null === $channel) {
            throw new SlackException('Channel is required');
        }

        $this->addAttachment(new TextAttachment(
            \sprintf('About %s', config('app.name')),
            \sprintf(
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
            TextAttachment::COLOR_INFO
        ));

        if (null === $this->chatUser) {
            $this->addHelpForUnlinkedUser();
        }

        $this->addHelpForUnlinkedChannel();
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
        $this->addAttachment(new TextAttachment(
            'Commands for unregistered channels:',
            '· `help` - Show help' . \PHP_EOL
                . '· `XdY[+C] [text]` - Roll X dice with Y sides, '
                . 'optionally adding C to the result, optionally '
                . 'describing that the roll is for "text"' . \PHP_EOL
                . '· `register <system>` - Register this channel for '
                . 'system code <system>, where system is one of:'
                . implode(', ', $systems) . \PHP_EOL
                . $this->getCampaignsHelp(),
            TextAttachment::COLOR_INFO
        ));
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
