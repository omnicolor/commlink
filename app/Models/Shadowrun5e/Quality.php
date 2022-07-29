<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

/**
 * Quality (positive or negative) a character possesses.
 */
class Quality
{
    /**
     * Description of the quality and its effects.
     * @var string
     */
    public string $description;

    /**
     * List of the quality's effects.
     * @var array<string, mixed>
     */
    public array $effects = [];

    /**
     * ID of the quality.
     * @var string
     */
    public string $id;

    /**
     * List of qualities or augmentations this is incompatible with.
     * @var string[]
     */
    public array $incompatibilities = [];

    /**
     * Amount of karma quality is worth.
     * @var int
     */
    public int $karma;

    /**
     * Name of the quality.
     * @var string
     */
    public string $name;

    /**
     * Book quality is described in.
     * @var string
     */
    public string $ruleset = 'core';

    /**
     * Type of quality for those that need it.
     * @var ?string
     */
    public ?string $type;

    /**
     * List of all qualities.
     * @var ?array<string, mixed>
     */
    public static ?array $qualities;

    /**
     * Build a new quality object.
     * @param string $id ID of the quality to load
     * @param array<string, mixed> $raw Raw quality from the data store
     * @throws \RuntimeException If the ID is invalid
     */
    public function __construct(string $id, array $raw = [])
    {
        $filename = config('app.data_path.shadowrun5e') . 'qualities.php';
        self::$qualities ??= require $filename;
        $id = \strtolower($id);
        if (!isset(self::$qualities[$id])) {
            throw new \RuntimeException(\sprintf(
                'Quality ID "%s" is invalid',
                $id
            ));
        }

        $quality = self::$qualities[$id];

        $this->description = $quality['description'];
        $this->effects = $quality['effects'] ?? [];
        $this->id = $id;
        $this->incompatibilities = $quality['incompatible-with'] ?? [];
        $this->karma = $quality['karma'];
        $this->name = $quality['name'];
        $this->ruleset = $quality['ruleset'] ?? 'core';

        if ('Addiction' === $quality['name']) {
            $this->name .= ' (' . $quality['severity'];
            if (isset($raw['addiction'])) {
                $this->name .= ' - ' . $raw['addiction'];
            }
            $this->name .= ')';
        } elseif (isset($raw['limits'])) {
            $limits = $raw['limits'];
            $this->effects = [
                'mental-limit' => 0,
                'physical-limit' => 0,
                'social-limit' => 0,
            ];
            $this->name .= ' (' . \implode(', ', $limits) . ')';
            foreach ($limits as $limit) {
                $this->effects[$limit . '-limit']++;
            }
        } elseif ('Allergy' === $quality['name']) {
            $this->name .= ' (' . $quality['severity'];
            if (isset($raw['allergy'])) {
                $this->name .= ' - ' . $raw['allergy'];
            }
            $this->name .= ')';
        } elseif (0 === \strpos($quality['id'], 'aptitude-')) {
            $start = (int)\strpos($quality['id'], '-') + 1;
            $this->name .= ' (' . \ucfirst(\substr($quality['id'], $start)) . ')';
        } elseif (0 === \strpos($quality['id'], 'exceptional-attribute-')) {
            $start = (int)\strrpos($quality['id'], '-') + 1;
            $this->name .= ' (' . \ucfirst(\substr($quality['id'], $start)) . ')';
        } elseif ('mentor-spirit' === $id && isset($raw['severity'])) {
            $spirit = MentorSpirit::findByName($raw['severity']);
            $this->description = $spirit->description;
            $this->name .= ' - ' . $spirit;
        } elseif (isset($quality['severity'])) {
            $this->name .= ' (' . \ucfirst($quality['severity']) . ')';
        }
    }

    /**
     * Return the quality's name.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return a quality based on its name.
     * @param string $name
     * @return Quality
     * @throws \RuntimeException
     */
    public static function findByName(string $name): Quality
    {
        $filename = config('app.data_path.shadowrun5e') . 'qualities.php';
        self::$qualities ??= require $filename;
        foreach (self::$qualities as $quality) {
            if (\strtolower($quality['name']) === \strtolower($name)) {
                return new Quality($quality['id']);
            }
        }
        throw new \RuntimeException(\sprintf(
            'Quality name "%s" was not found',
            $name
        ));
    }
}
