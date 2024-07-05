<?php

declare(strict_types=1);

namespace App\Services\Omae;

use App\Services\ConverterInterface;
use Illuminate\Support\Str;
use Modules\Shadowrun5e\Models\ActiveSkill;
use Modules\Shadowrun5e\Models\Armor;
use Modules\Shadowrun5e\Models\ArmorModification;
use Modules\Shadowrun5e\Models\Augmentation;
use Modules\Shadowrun5e\Models\PartialCharacter;
use Modules\Shadowrun5e\Models\Quality;
use Modules\Shadowrun5e\Models\Weapon;
use Modules\Shadowrun5e\Models\WeaponModification;
use RuntimeException;

/**
 * Importer class for Omae Shadowrun 5E profiles.
 * @psalm-suppress UnusedClass
 */
class Shadowrun5eConverter implements ConverterInterface
{
    protected const REQUIRED_NUM_PRIORITIES = 5;

    /**
     * @var PartialCharacter Character being converted.
     */
    protected PartialCharacter $character;

    /**
     * Errors encountered and not handled.
     * @var array<int, string>
     */
    protected array $errors = [];

    /**
     * Omae's file as an array.
     * @var array<int, string>
     */
    protected array $file;

    protected const AUGMENTATION_MAP = [
        'Cybereyes Basic System' => 'cybereyes-1',
    ];

    protected const WEAPON_MODIFIATION_MAP = [
        'Smartgun System, Internal' => 'smartlink-internal',
    ];

    /**
     * Constructor.
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        if (!file_exists($filename)) {
            throw new RuntimeException('Unable to locate Omae file');
        }
        if ('text/plain' !== mime_content_type($filename)) {
            throw new RuntimeException('File does not appear to be an Omae file');
        }
        $file = file(
            $filename,
            \FILE_IGNORE_NEW_LINES | \FILE_SKIP_EMPTY_LINES
        );
        if (false === $file) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException('Could not load Omae file');
            // @codeCoverageIgnoreEnd
        }
        $this->file = $file;
        $this->character = new PartialCharacter();
    }

    public function convert(): PartialCharacter
    {
        while (!$this->endOfFile()) {
            $this->skipToMajorHeading();

            $nextline = (string)array_shift($this->file);
            switch (substr($nextline, 2)) {
                case 'Priority':
                    $this->parsePriorities();
                    break;
                case 'Personal Details':
                    $this->parseDetails();
                    break;
                case 'Attributes':
                    $this->parseAttributes();
                    break;
                case 'Qualities':
                    $this->parseQualities();
                    break;
                case 'Magic/Resonance':
                    $this->parseAwakened();
                    break;
                case 'Skills':
                    $this->parseSkills();
                    break;
                case 'Street Gear':
                    $this->parseGear();
                    break;
                default:
                    $this->errors[] = sprintf(
                        'Found unhandled section "%s"',
                        substr($nextline, 2)
                    );
                    break;
            }
        }
        return $this->character;
    }

    protected function endOfFile(): bool
    {
        return 0 === count($this->file);
    }

    protected function nextLineIsMajorHeading(): bool
    {
        $nextline = Str::of($this->file[0]);
        return $nextline->startsWith('##') && !$nextline->startsWith('###');
    }

    protected function nextLineIsMinorHeading(): bool
    {
        return Str::of($this->file[0])->startsWith('###');
    }

    protected function nextLineIsData(): bool
    {
        return !$this->endOfFile()
            && !$this->nextLineIsMajorHeading()
            && !$this->nextLineIsMinorHeading();
    }

    protected function skipToMajorHeading(): void
    {
        while (!$this->endOfFile() && !$this->nextLineIsMajorHeading()) {
            array_shift($this->file);
        }
    }

    protected function skipToMinorHeading(): void
    {
        while ($this->nextLineIsData()) {
            array_shift($this->file);
        }
    }

    protected function parsePriorities(): void
    {
        $this->removeHeader();

        $priorities = explode(' | ', (string)array_shift($this->file));
        if (self::REQUIRED_NUM_PRIORITIES !== count($priorities)) {
            $this->errors[] = 'Invalid priorities listed';
            return;
        }
        $priorities = array_combine(
            [
                'metatypePriority',
                'attributePriority',
                'magicPriority',
                'skillPriority',
                'resourcePriority',
            ],
            $priorities
        );
        $this->character->priorities = array_merge(
            $this->character->priorities ?? [],
            $priorities,
        );
    }

    protected function parseDetails(): void
    {
        while (!$this->endOfFile() && !$this->nextLineIsMajorHeading()) {
            $line = Str::of((string)array_shift($this->file));
            if ($line->startsWith('**Name/Alias:** ')) {
                if ($line->contains('[Insert name]')) {
                    $this->character->handle = 'Unnamed Omae import';
                    continue;
                }
                $names = $line->substr(16)->explode('/');
                $this->character->realName = $names[0];
                $this->character->handle = $names[1] ?? 'Unnamed character';
                continue;
            }
            if ($line->startsWith('**Metatype:** ')) {
                $this->character->priorities = array_merge(
                    $this->character->priorities ?? [],
                    ['metatype' => (string)$line->substr(14)],
                );
                continue;
            }
            if ($line->startsWith('Karma')) {
                // Ignore the separator line
                array_shift($this->file);
                $karmas = explode(' | ', (string)array_shift($this->file));
                $this->character->karma = (int)$karmas[1];
                $this->character->karmaCurrent = (int)$karmas[0];
                continue;
            }
        }
    }

    protected function parseAttributes(): void
    {
        $this->removeHeader();
        $attributes = explode(' | ', (string)array_shift($this->file));

        $this->character->body = (int)$attributes[0];
        $this->character->agility = (int)$attributes[1];
        $this->character->reaction = (int)$attributes[2];
        $this->character->strength = (int)$attributes[3];
        $this->character->willpower = (int)$attributes[4];
        $this->character->logic = (int)$attributes[5];
        $this->character->intuition = (int)$attributes[6];
        $this->character->charisma = (int)$attributes[7];
        $this->character->edge = $this->character->edgeCurrent
            = (int)$attributes[8];

        // Note: That's not a normal dash...
        if ('–' !== $attributes[9]) {
            $this->character->magic = (int)$attributes[9];
        }

        // We'll calculate the limits on the fly.
        while (!$this->endOfFile() && !$this->nextLineIsMajorHeading()) {
            array_shift($this->file);
        }
    }

    protected function parseQualities(): void
    {
        $this->removeHeader();

        $qualities = [];
        while (!$this->endOfFile() && !$this->nextLineIsMajorHeading()) {
            $qualityName = explode(' | ', (string)array_shift($this->file))[0];
            try {
                $quality = Quality::findByName($qualityName);
                $qualities[] = ['id' => $quality->id];
            } catch (RuntimeException $ex) {
                $this->errors[] = $ex->getMessage();
            }
        }
        $this->character->qualities = $qualities;
    }

    protected function parseAwakened(): void
    {
        /*
        $type = (string)Str::of((string)array_shift($this->file))->after(' ');
        switch ($type) {
            case 'Adept':
            case 'Aspected':
            case 'Mage':
            case 'Mystic':
            case 'Technomancer':
        }
         */
        $this->skipToMajorHeading();
    }

    protected function parseSkills(): void
    {
        while (!$this->endOfFile() && !$this->nextLineIsMajorHeading()) {
            $this->skipToMinorHeading();

            $skillType = substr((string)array_shift($this->file), 3);
            switch ($skillType) {
                case 'Active':
                    $this->parseActiveSkills();
                    break;
                default:
                    $this->errors[] = sprintf(
                        'Unknown skill type "%s"',
                        $skillType
                    );
                    break;
            }
        }
    }

    protected function parseActiveSkills(): void
    {
        $this->removeHeader();

        $skills = [];
        while ($this->nextLineIsData()) {
            [$name, $level, , $specialization] = explode(
                ' | ',
                (string)array_shift($this->file)
            );
            try {
                // Make sure the skill is valid.
                $id = ActiveSkill::findIdByName($name);
            } catch (RuntimeException $ex) {
                $this->errors[] = $ex->getMessage();
                continue;
            }
            $skill = [
                'id' => $id,
                'level' => (int)$level,
            ];
            if ('–' !== $specialization) {
                $skill['specialization'] = $specialization;
            }
            $skills[] = $skill;
        }
        $this->character->skills = $skills;
    }

    protected function parseGear(): void
    {
        while (!$this->endOfFile() && !$this->nextLineIsMajorHeading()) {
            $gearType = Str::of((string)array_shift($this->file))
                ->substr(4)
                ->lower();
            switch ((string)$gearType) {
                case 'ammunition':
                    // Ignore ammunition. Omae doesn't attach to a specific
                    // weapon or track quantity.
                    $this->skipToMinorHeading();
                    break;
                case 'armors':
                    $this->parseArmor();
                    break;
                case 'augmentations':
                    $this->parseAugmentations();
                    break;
                case 'commlinks':
                case 'contracts/upkeep':
                case 'id/credsticks':
                case 'lifestyles':
                    $this->parseLifestyles();
                    break;
                case 'weapons':
                    $this->parseWeapons();
                    break;
                default:
                    $this->errors[] = sprintf(
                        'Unknown gear category "%s"',
                        $gearType
                    );
                    $this->skipToMinorHeading();
                    break;
            }
        }
    }

    protected function parseArmor(): void
    {
        $this->removeHeader();
        $armors = [];
        while (!$this->endOfFile() && !$this->nextLineIsMinorHeading()) {
            [$name, , , $mods] = explode(
                ' | ',
                (string)array_shift($this->file)
            );
            $armor = [];
            try {
                $armor['id'] = Armor::findByName($name)->id;
            } catch (RuntimeException $ex) {
                $this->errors[] = $ex->getMessage();
                continue;
            }

            if ('N/A' === $mods) {
                $armors[] = $armor;
                continue;
            }

            $armor['modifications'] = [];
            foreach (explode('; ', $mods) as $mod) {
                $rating = null;
                $mod = Str::of($mod);
                if (is_numeric((string)$mod->afterLast(' '))) {
                    $rating = (string)$mod->afterLast(' ');
                    $rating = (int)$rating;
                    $mod = $mod->beforeLast(' ');
                }
                try {
                    $armor['modifications'][] = ArmorModification::findByName(
                        (string)$mod,
                        $rating
                    )->id;
                } catch (RuntimeException $ex) {
                    $this->errors[] = $ex->getMessage();
                }
            }
            $armors[] = $armor;
        }
        $this->character->armor = $armors;
    }

    protected function parseAugmentations(): void
    {
        $this->removeHeader();
        $augmentations = [];
        while (!$this->endOfFile() && !$this->nextLineIsMinorHeading()) {
            [$name] = explode(' | ', (string)array_shift($this->file));

            if (isset(self::AUGMENTATION_MAP[$name])) {
                $augmentations[] = ['id' => self::AUGMENTATION_MAP[$name]];
                continue;
            }

            $rating = null;
            if (false !== str_contains($name, '(')) {
                [$name, $rating] = explode(' (', $name);
                $rating = substr($rating, 0, -1);
            }
            try {
                $augmentations[] = [
                    'id' => (Augmentation::findByName($name, $rating))->id,
                ];
            } catch (RuntimeException $ex) {
                $this->errors[] = $ex->getMessage();
                continue;
            }
        }

        $this->character->augmentations = $augmentations;
    }

    protected function parseLifestyles(): void
    {
        $this->skipToMinorHeading();
    }

    protected function parseWeapons(): void
    {
        $this->removeHeader();
        $weapons = [];
        while (!$this->endOfFile() && !$this->nextLineIsMinorHeading()) {
            [$name, , , , , $mods] = explode(
                ' | ',
                (string)array_shift($this->file)
            );
            try {
                $weapon = [
                    'id' => (Weapon::findByName($name))->id,
                ];
            } catch (RuntimeException $ex) {
                $this->errors[] = $ex->getMessage();
                continue;
            }
            if ('N/A' !== $mods) {
                $weapon['modifications'] = [];
                foreach (explode('; ', $mods) as $mod) {
                    if (isset(self::WEAPON_MODIFIATION_MAP[$mod])) {
                        $weapon['modifications'][]
                            = self::WEAPON_MODIFIATION_MAP[$mod];
                        continue;
                    }
                    try {
                        $weaponMod = WeaponModification::findByName($mod);
                        $weapon['modifications'][] = $weaponMod->id;
                    } catch (RuntimeException $ex) {
                        $this->errors[] = $ex->getMessage();
                    }
                }
            }
            $weapons[] = $weapon;
        }
        $this->character->weapons = $weapons;
    }

    protected function removeHeader(): void
    {
        // Get rid of header.
        array_shift($this->file);
        // Get rid of separator.
        array_shift($this->file);
    }

    /**
     * Return any errors that happened during conversion.
     * @return array<int, string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
