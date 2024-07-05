<?php

declare(strict_types=1);

namespace App\Services\WorldAnvil;

use App\Services\ConverterInterface;
use Illuminate\Support\Str;
use JsonException;
use Modules\Expanse\Models\Focus;
use Modules\Expanse\Models\PartialCharacter;
use Modules\Expanse\Models\Talent;
use RuntimeException;
use stdClass;

use function file_exists;
use function file_get_contents;
use function json_decode;
use function sprintf;

use const JSON_THROW_ON_ERROR;
use const PHP_EOL;

class ExpanseConverter implements ConverterInterface
{
    public const string TEMPLATE_ID = '3892';

    /**
     * @var array<int, string>
     */
    protected array $errors = [];
    protected PartialCharacter $character;
    protected stdClass $rawCharacter;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(string $filename)
    {
        if (!file_exists($filename)) {
            throw new RuntimeException('Unable to locate World Anvil file');
        }

        try {
            $this->rawCharacter = json_decode(
                json: (string)file_get_contents($filename),
                associative: false,
                flags: JSON_THROW_ON_ERROR,
            );
        } catch (JsonException $ex) {
            throw new RuntimeException(
                'File does not appear to be a World Anvil file'
            );
        }

        if (
            !isset($this->rawCharacter->templateId)
            || self::TEMPLATE_ID !== $this->rawCharacter->templateId
        ) {
            throw new RuntimeException('Character is not an Expanse character');
        }
    }

    public function convert(): PartialCharacter
    {
        $this->character = new PartialCharacter([
            'accuracy' => (int)$this->rawCharacter->accuracy,
            'age' => (int)$this->rawCharacter->age,
            'appearance' => $this->rawCharacter->appearance,
            'background' => $this->rawCharacter->background,
            'communication' => (int)$this->rawCharacter->communication,
            'constitution' => (int)$this->rawCharacter->constitution,
            'dexterity' => (int)$this->rawCharacter->dexterity,
            'drive' => $this->rawCharacter->drive,
            'experience' => (int)$this->rawCharacter->experience,
            'fighting' => (int)$this->rawCharacter->fighting,
            'gender' => $this->rawCharacter->gender,
            'intelligence' => (int)$this->rawCharacter->intelligence,
            'level' => (int)$this->rawCharacter->level,
            'name' => $this->rawCharacter->name,
            'origin' => $this->rawCharacter->origin,
            'perception' => (int)$this->rawCharacter->perception,
            'profession' => $this->rawCharacter->profession,
            'socialClass' => Str::remove(
                ' class',
                $this->rawCharacter->social_class,
                false,
            ),
            'strength' => (int)$this->rawCharacter->strength,
            'willpower' => (int)$this->rawCharacter->willpower,
        ]);
        $this->parseTalents()
            ->parseFocuses();
        return $this->character;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Commlink stores focuses as an array and knows out what attribute they're
     * linked to. World Anvil stores them attached to each attribute.
     */
    protected function parseFocuses(): self
    {
        $attributes = [
            'accuracy',
            'communication',
            'constitution',
            'dexterity',
            'fighting',
            'intelligence',
            'perception',
            'strength',
            'willpower',
        ];
        $focuses = [];
        foreach ($attributes as $attribute) {
            $focus = $attribute . '_focus';
            $focus = $this->rawCharacter->$focus;
            if ('' === $focus) {
                continue;
            }
            // World Anvil doesn't give any guidance on how to store multiple
            // focuses for one attribute.
            if (!Str::contains($focus, [PHP_EOL, ','])) {
                try {
                    $focus = new Focus(Str::trim($focus));
                    $focuses[] = ['id' => $focus->id];
                } catch (RuntimeException) {
                    $this->errors[] = sprintf('Invalid focus "%s"', $focus);
                }
                continue;
            }

            $focusArray = Str::of($focus)
                ->replace(PHP_EOL, ',')
                ->squish()
                ->replace(' ', ',')
                ->explode(',');
            foreach ($focusArray as $value) {
                $focus = Str::trim($value);
                if ('' === $focus) {
                    continue;
                }
                try {
                    $focus = new Focus($focus);
                    $focuses[] = ['id' => $focus->id];
                } catch (RuntimeException) {
                    $this->errors[] = sprintf('Invalid focus "%s"', $focus);
                }
            }
        }
        $this->character->focuses = $focuses;
        return $this;
    }

    protected function parseTalents(): self
    {
        $talents = [];
        // World Anvil sheets have room for 20 talents.
        for ($i = 1; $i < 20; $i++) {
            $property = 'talent_name_' . Str::padLeft((string)$i, 2, '0');
            $talent = strtolower($this->rawCharacter->$property);
            if ('' === $talent) {
                continue;
            }
            $level = 'talent_degree_' . Str::padLeft((string)$i, 2, '0');
            $level = match ($level) {
                'novice' => Talent::NOVICE,
                'expert' => Talent::EXPERT,
                'master' => Talent::MASTER,
                'journeyman' => Talent::JOURNEYMAN,
                default => Talent::NOVICE,
            };
            $talents[] = [
                'name' => $talent,
                'level' => $level,
            ];
        }
        $this->character->talents = $talents;
        return $this;
    }
}
