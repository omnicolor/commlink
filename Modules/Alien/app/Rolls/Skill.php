<?php

declare(strict_types=1);

namespace Modules\Alien\Rolls;

use App\Events\MessageReceived;
use App\Models\Channel;
use App\Rolls\Roll;

use function array_shift;
use function explode;
use function sprintf;
use function trim;

/**
 * @psalm-suppress UnusedClass
 */
class Skill extends Number
{
    protected int $dice;
    protected ?string $error = null;
    protected int $panics = 0;
    protected array $rolls = [];
    protected int $successes = 0;
    protected int $stress;

    /**
     * @phpstan-ignore constructor.missingParentCall
     */
    public function __construct(
        string $content,
        string $username,
        Channel $channel,
        public ?MessageReceived $event = null,
    ) {
        Roll::__construct($content, $username, $channel);

        if (null === $this->character) {
            $this->error = 'Skill rolls are only available if you have linked '
                . 'a character';
            return;
        }

        $args = explode(' ', trim($content));
        // Ignore the word 'skill'.
        array_shift($args);

        $skill = array_shift($args);
        if (!isset($this->character->skills[$skill])) {
            $this->error = sprintf('Skill "%s" is not valid', $skill);
            return;
        }
        $skill = $this->character->skills[$skill];

        // @phpstan-ignore property.dynamicName
        $this->dice = $skill->rank + $this->character->{$skill->attribute};
        $this->stress = $this->character->stress ?? 0;
        $this->description = sprintf(
            ' for %s (%d+%d+%d)',
            $skill->name,
            $skill->rank,
            // @phpstan-ignore property.dynamicName
            $this->character->{$skill->attribute},
            $this->character->stress ?? 0,
        );

        $this->roll();
    }
}
