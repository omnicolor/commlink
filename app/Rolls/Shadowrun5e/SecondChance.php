<?php

declare(strict_types=1);

namespace App\Rolls\Shadowrun5e;

use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Shadowrun5e\Character;
use App\Models\Slack\TextAttachment;
use App\Traits\PrettifyRollsForSlack;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Http;

class SecondChance
{
    use PrettifyRollsForSlack;

    protected Channel $channel;
    protected ?Character $character;

    public function __construct(public \stdClass $request)
    {
        $this->channel = $this->getChannel(
            $request->team->id,
            $request->channel->id
        );
        $this->channel->user = $request->user->id;
        // @phpstan-ignore-next-line
        $this->character = $this->channel->character();
    }

    public function handle(): ?SlackResponse
    {
        if (null === $this->character) {
            $response_url = $this->request->response_url;
            $error_response = [
                'color' => TextAttachment::COLOR_DANGER,
                'delete_original' => false,
                'replace_original' => false,
                'response_type' => 'ephemeral',
                'text' => 'Only registered users can click action buttons',
            ];
            Http::post($response_url, $error_response);
            return null;
        }

        if ($this->character->id !== $this->request->callback_id) {
            $response_url = $this->request->response_url;
            $error_response = [
                'color' => TextAttachment::COLOR_DANGER,
                'delete_original' => false,
                'replace_original' => false,
                'response_type' => 'ephemeral',
                'text' => 'Only the user that rolled can use this action',
            ];
            Http::post($response_url, $error_response);
            return null;
        }

        $original_message = $this->request->original_message->attachments[0];
        $original_roll = $original_message->footer;
        $original_roll = str_replace(['*', '~'], ['', ''], $original_roll);
        /** @var array<int, int> */
        $original_roll = explode(' ', $original_roll);

        $successes = 0;
        $rerolled = 0;
        foreach ($original_roll as $key => $roll) {
            if (5 <= $roll) {
                $original_roll[$key] = (int)$roll;
                $successes++;
                continue;
            }
            $rerolled++;
            $roll = random_int(1, 6);
            if (5 <= $roll) {
                $successes++;
            }
            $original_roll[$key] = $roll;
        }

        \rsort($original_roll);
        return (new SlackResponse(channel: $this->channel))
            ->addAttachment(new TextAttachment(
                title: $original_message->title . ' with second chance',
                text: sprintf(
                    'Rolled %d successes (originally %s), rerolled %d',
                    $successes,
                    strtolower($original_message->text),
                    $rerolled
                ),
                color: TextAttachment::COLOR_SUCCESS,
                footer: implode(' ', $this->prettifyRolls($original_roll)),
            ));
    }

    /**
     * Get the channel attached to the request.
     * @param string $team Slack team ID (server)
     * @param string $channel Slack channel ID
     * @return Channel
     */
    protected function getChannel(string $team, string $channel): Channel
    {
        try {
            return Channel::slack()
                ->where('channel_id', $channel)
                ->where('server_id', $team)
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            return new Channel([
                'channel_id' => $channel,
                'server_id' => $team,
                'type' => Channel::TYPE_SLACK,
            ]);
        }
    }
}
