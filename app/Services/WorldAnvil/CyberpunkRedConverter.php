<?php

declare(strict_types=1);

namespace App\Services\WorldAnvil;

use App\Services\ConverterInterface;
use Error;
use Illuminate\Support\Str;
use JsonException;
use Modules\Cyberpunkred\Models\Armor;
use Modules\Cyberpunkred\Models\PartialCharacter;
use Modules\Cyberpunkred\Models\Weapon;
use RuntimeException;
use stdClass;

use function explode;
use function sprintf;
use function strtolower;

use const JSON_THROW_ON_ERROR;

class CyberpunkRedConverter implements ConverterInterface
{
    public const string TEMPLATE_ID = '6836';

    /**
     * @var array<int, string>
     */
    protected array $errors = [];
    protected PartialCharacter $character;
    protected stdClass $rawCharacter;

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
            throw new RuntimeException(
                'Character is not a Cyberpunk Red character'
            );
        }
        $this->character = new PartialCharacter();
    }

    public function convert(): PartialCharacter
    {
        $this->character->handle = $this->rawCharacter->handle;
        $this->character->name = $this->rawCharacter->name;
        if ('' === $this->character->handle) {
            $this->character->handle = $this->character->name;
        }
        $this->setAttributes()
            ->parseLifepath()
            ->parseRoles()
            ->parseSkills()
            ->parseArmor()
            ->parseWeapons();
        return $this->character;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * TODO: add support for custom armor.
     */
    protected function parseArmor(): self
    {
        $armors = [
            'head' => null,
            'body' => null,
            'shield' => null,
        ];
        $armorName = (string)$this->rawCharacter->armor_head;
        if ('None' !== $armorName) {
            try {
                $armors['head'] = (Armor::findByName($armorName))->id;
            } catch (RuntimeException $ex) {
                $this->errors[] = $ex->getMessage();
            }
        }
        $armorName = (string)$this->rawCharacter->armor_body;
        if ('None' !== $armorName) {
            try {
                $armors['body'] = (Armor::findByName($armorName))->id;
            } catch (RuntimeException $ex) {
                $this->errors[] = $ex->getMessage();
            }
        }
        $armorName = (string)$this->rawCharacter->armor_shield;
        if ('None' !== $armorName) {
            try {
                $armors['shield'] = (Armor::findByName($armorName))->id;
            } catch (RuntimeException $ex) {
                $this->errors[] = $ex->getMessage();
            }
        }

        $this->character->armor = $armors;
        return $this;
    }

    protected function parseRoles(): self
    {
        $role = $this->rawCharacter->role;
        $class = sprintf('Modules\\Cyberpunkred\\Models\\Role\\%s', $role);
        try {
            $role = new $class();
        } catch (Error) {
            $this->errors[] = sprintf('Role "%s" is invalid', $role);
            return $this;
        }

        $this->character->roles = [[
            'role' => strtolower($this->rawCharacter->role),
            'rank' => (int)$this->rawCharacter->role_ability_rank,
        ]];
        return $this;
    }

    protected function parseSkills(): self
    {
        $filename = config('cyberpunkred.data_path') . 'world-anvil.skills.php';
        $anvilSkillMap = require $filename;
        $filename = config('cyberpunkred.data_path') . 'skills.php';
        $rawSkills = require $filename;

        $skills = [];
        $customSkills = [];
        for ($i = 1; $i <= 75; $i++) {
            $id = Str::padLeft((string)$i, 2, '0');
            $anvilId = 'skill_value_' . $id;
            $level = (int)$this->rawCharacter->$anvilId;
            if (0 >= $level) {
                // Character doesn't have ranks in the skill, leave it out.
                continue;
            }

            $commlinkId = (string)$anvilSkillMap[$anvilId];
            if (!isset($rawSkills[$commlinkId])) {
                // Custom skill?
                $name = 'skill_name_' . $id;
                if (!isset($this->rawCharacter->$name)) {
                    // Maybe the data file is incomplete...
                    $this->errors[] = sprintf(
                        'World Anvil skill "%s" not found',
                        $id
                    );
                    continue;
                }

                $name = $this->rawCharacter->$name;
                [$type, $name] = explode(': ', $name);
                $customSkills[] = [
                    'level' => $level,
                    'name' => $name,
                    'type' => $type,
                ];
                continue;
            }
            $skills[$commlinkId] = $level;
        }

        $this->character->skills = $skills;
        if (0 !== count($customSkills)) {
            $this->character->skills_custom = $customSkills;
        }
        return $this;
    }

    protected function parseLifepath(): self
    {
        $this->character->lifepath = [
            'childhood-environment' => $this->rawCharacter->childhood_environment,
            'clothing-style' => $this->rawCharacter->clothing_style,
            'cultural-origins' => $this->rawCharacter->cultural_origins,
            'family-background' => $this->rawCharacter->family_background,
            'family-crisis' => $this->rawCharacter->family_crisis,
            'hairstyle' => $this->rawCharacter->hairstyle,
            'life-goals' => $this->rawCharacter->life_goals,
            'people-feelings' => $this->rawCharacter->feeling_about_people,
            'person-valued' => $this->rawCharacter->most_valued_person,
            'personality' => $this->rawCharacter->personality,
            'possession-valued' => $this->rawCharacter->most_valued_possession,
            'what-valued' => $this->rawCharacter->value_most,
        ];
        return $this;
    }

    protected function parseWeapons(): self
    {
        $weapons = [];
        for ($i = 1; $i <= 10; $i++) {
            $id = Str::padLeft((string)$i, 2, '0');
            $name = 'weapon_name_' . $id;
            if ('' === $this->rawCharacter->$name) {
                continue;
            }
            $name = $this->rawCharacter->$name;
            try {
                $weapon = Weapon::findByName($name);
            } catch (RuntimeException $ex) {
                $this->errors[] = $ex->getMessage();
                continue;
            }

            $ammo = 'weapon_ammo_' . $id;
            if ('' !== $this->rawCharacter->$ammo) {
                $weapons[] = [
                    'id' => $weapon->id,
                    'ammoRemaining' => (int)$this->rawCharacter->$ammo,
                ];
                continue;
            }
            $weapons[] = ['id' => $weapon->id];
        }
        $this->character->weapons = $weapons;
        return $this;
    }

    protected function setAttributes(): self
    {
        $this->character->body = (int)$this->rawCharacter->body;
        $this->character->cool = (int)$this->rawCharacter->cool;
        $this->character->dexterity = (int)$this->rawCharacter->dex;
        $this->character->empathy = (int)$this->rawCharacter->emp_max;
        $this->character->empathy_current = (int)$this->rawCharacter->emp_curr;
        $this->character->intelligence = (int)$this->rawCharacter->int;
        $this->character->luck = (int)$this->rawCharacter->luck_max;
        $this->character->luck_current = (int)$this->rawCharacter->luck_curr;
        $this->character->movement = (int)$this->rawCharacter->move;
        $this->character->reflexes = (int)$this->rawCharacter->ref;
        $this->character->technique = (int)$this->rawCharacter->tech;
        $this->character->willpower = (int)$this->rawCharacter->will;
        $this->character->hit_points_current = (int)$this->rawCharacter->hitpoints_curr;
        $this->character->humanity_current = (int)$this->rawCharacter->humanity_curr;
        $this->character->improvement_points_current = (int)$this->rawCharacter->improvement_points_curr;
        $this->character->improvement_points = (int)$this->rawCharacter->improvement_points_max;
        $this->character->reputation = (int)$this->rawCharacter->reputation;
        return $this;
    }
}
