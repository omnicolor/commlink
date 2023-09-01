<?php

declare(strict_types=1);

namespace App\Services\WorldAnvil;

use App\Models\Cyberpunkred\PartialCharacter;
use App\Services\ConverterInterface;
use Error;
use Illuminate\Support\Str;
use JsonException;
use RuntimeException;
use stdClass;

class CyberpunkRedConverter implements ConverterInterface
{
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
        if ('application/json' !== mime_content_type($filename)) {
            throw new RuntimeException('File does not appear to be a World Anvil file');
        }

        $characterJson = file_get_contents($filename);
        if (false === $characterJson) {
            throw new RuntimeException('Could not load World Anvil file');
        }

        try {
            $this->rawCharacter = json_decode(
                json: $characterJson,
                associative: false,
                flags: \JSON_THROW_ON_ERROR,
            );
        } catch (JsonException $ex) {
            throw new RuntimeException($ex->getMessage());
        }

        if ('6836' !== $this->rawCharacter->templateId) {
            throw new RuntimeException('Character is not a Cyberpunk Red character');
        }
    }

    public function convert(): PartialCharacter
    {
        $this->character = new PartialCharacter();
        $this->character->handle = $this->rawCharacter->handle;
        $this->character->name = $this->rawCharacter->name;
        if ('' === $this->character->handle) {
            $this->character->handle = $this->character->name;
        }
        $this->setAttributes()
            ->parseLifepath()
            ->parseRoles()
            ->parseSkills();
        return $this->character;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function parseRoles(): self
    {
        $role = $this->rawCharacter->role;
        $class = \sprintf('App\\Models\\Cyberpunkred\\Role\\%s', $role);
        try {
            $role = new $class();
        } catch (Error) {
            $this->errors[] = \sprintf('Role "%s" is invalid', $role);
            return $this;
        }

        $this->character->roles = [[
            'role' => \strtolower($this->rawCharacter->role),
            'rank' => (int)$this->rawCharacter->role_ability_rank,
        ]];
        return $this;
    }

    /**
     * @psalm-suppress UnresolvableInclude
     */
    public function parseSkills(): self
    {
        $filename = config('app.data_path.cyberpunkred') . 'skills.php';
        $rawSkills = require $filename;
        foreach ($rawSkills as $id => $skill) {
            $rawSkills[$id] = $skill['world_anvil_id'] ?? null;
        }
        $rawSkills = array_flip(array_filter($rawSkills));
        $skills = [];
        $customSkills = [];
        for ($i = 1; $i <= 75; $i++) {
            $id = Str::padLeft((string)$i, 2, '0');
            $anvilId = 'skill_value_' . $id;
            $level = (int)$this->rawCharacter->$anvilId;
            if (0 === $level) {
                continue;
            }
            if (!isset($rawSkills[$id])) {
                // Custom skill, or data files are incomplete...
                $name = 'skill_name_' . $id;
                if (!isset($this->rawCharacter->$name)) {
                    $this->errors[] = sprintf('Skill "%s" not found', $id);
                    continue;
                }
                $name = $this->rawCharacter->$name;
                [$type, $name] = \explode(': ', $name);
                $customSkills[] = [
                    'level' => $level,
                    'name' => $name,
                    'type' => $type,
                ];
                continue;
            }
            $skillId = $rawSkills[$id];
            $skills[$skillId] = $level;
        }
        $this->character->skills = $skills;
        if (0 !== count($customSkills)) {
            $this->character->skills_custom = $customSkills;
        }
        return $this;
    }

    public function parseLifepath(): self
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
        $this->character->humanity = (int)$this->rawCharacter->humanity_max;
        $this->character->humanity_current = (int)$this->rawCharacter->humanity_curr;
        $this->character->improvement_points_current = (int)$this->rawCharacter->improvement_points_curr;
        $this->character->improvement_points = (int)$this->rawCharacter->improvement_points_max;
        $this->character->reputation = (int)$this->rawCharacter->reputation;
        return $this;
    }
}
