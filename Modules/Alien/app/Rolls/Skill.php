<?php

declare(strict_types=1);

namespace Modules\Alien\Rolls;

use App\Models\Channel;
use App\Models\Character as BaseCharacter;
use App\Rolls\Roll;
use Modules\Alien\Models\Character;

use function array_shift;
use function explode;
use function sprintf;
use function trim;

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
    ) {
        Roll::__construct($content, $username, $channel);

        if (!$this->character instanceof BaseCharacter) {
            $this->error = 'Skill rolls are only available if you have linked '
                . 'a character';
            return;
        }
        /** @var Character $character */
        $character = $this->character;

        $args = explode(' ', trim($content));
        // Ignore the word 'skill'.
        array_shift($args);

        $skill = array_shift($args);
        if (!isset($character->skills[$skill])) {
            $this->error = sprintf('Skill "%s" is not valid', $skill);
            return;
        }
        $skill = $character->skills[$skill];

        // @phpstan-ignore property.dynamicName
        $this->dice = $skill->rank + $character->{$skill->attribute};
        $this->stress = $character->stress ?? 0;
        $this->description = sprintf(
            ' for %s (%d+%d+%d)',
            $skill->name,
            $skill->rank,
            // @phpstan-ignore property.dynamicName
            $character->{$skill->attribute},
            $character->stress ?? 0,
        );

        $this->roll();
    }

    /**
     * @return array{
     *   panic: bool,
     *   rolls: array<int, int>,
     *   success: bool,
     *   text: string,
     *   title: string,
     * }|array{error: string}
     */
    public function forWeb(): array
    {
        return [
            'panic' => 0 !== $this->panics,
            'pushable' => true,
            'rolls' => $this->rolls,
            'success' => 0 !== $this->successes,
            'text' => $this->formatText(),
            'title' => $this->formatTitle(),
        ];
    }
}
