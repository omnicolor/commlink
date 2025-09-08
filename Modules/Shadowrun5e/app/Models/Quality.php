<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use Override;
use RuntimeException;
use Stringable;

use function config;
use function implode;
use function sprintf;
use function strpos;
use function strrpos;
use function strtolower;
use function substr;
use function ucfirst;

/**
 * Quality (positive or negative) a character possesses.
 */
final class Quality implements Stringable
{
    public string $description;

    /**
     * List of the quality's effects.
     * @var array<string, mixed>
     */
    public array $effects = [];

    /**
     * List of qualities or augmentations this is incompatible with.
     * @var array<int, string>
     */
    public array $incompatibilities = [];
    public readonly int $karma;
    public string $name;
    public readonly string $ruleset;
    //public readonly null|string $type;

    /**
     * List of all qualities.
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $qualities;

    /**
     * Build a new quality object.
     * @param array<string, mixed> $raw Raw quality from the data store
     * @throws RuntimeException If the ID is invalid
     */
    public function __construct(public readonly string $id, array $raw = [])
    {
        $filename = config('shadowrun5e.data_path') . 'qualities.php';
        self::$qualities ??= require $filename;
        $id = strtolower($id);
        if (!isset(self::$qualities[$id])) {
            throw new RuntimeException(sprintf(
                'Quality ID "%s" is invalid',
                $id
            ));
        }

        $quality = self::$qualities[$id];

        $this->description = $quality['description'];
        $this->effects = $quality['effects'] ?? [];
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
            $this->name .= ' (' . implode(', ', $limits) . ')';
            foreach ($limits as $limit) {
                ++$this->effects[$limit . '-limit'];
            }
        } elseif ('Allergy' === $quality['name']) {
            $this->name .= ' (' . $quality['severity'];
            if (isset($raw['allergy'])) {
                $this->name .= ' - ' . $raw['allergy'];
            }
            $this->name .= ')';
        } elseif (str_starts_with((string)$quality['id'], 'aptitude-')) {
            $start = (int)strpos((string)$quality['id'], '-') + 1;
            $this->name .= ' (' . ucfirst(substr((string)$quality['id'], $start)) . ')';
        } elseif (str_starts_with((string)$quality['id'], 'exceptional-attribute-')) {
            $start = (int)strrpos((string)$quality['id'], '-') + 1;
            $this->name .= ' (' . ucfirst(substr((string)$quality['id'], $start)) . ')';
        } elseif ('mentor-spirit' === $id && isset($raw['severity'])) {
            $spirit = MentorSpirit::findByName($raw['severity']);
            $this->description = $spirit->description;
            $this->name .= ' - ' . $spirit;
        } elseif (isset($quality['severity'])) {
            $this->name .= ' (' . ucfirst((string)$quality['severity']) . ')';
        }
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return a quality based on its name.
     * @throws RuntimeException
     */
    public static function findByName(string $name): Quality
    {
        $filename = config('shadowrun5e.data_path') . 'qualities.php';
        self::$qualities ??= require $filename;
        foreach (self::$qualities ?? [] as $quality) {
            if (strtolower((string)$quality['name']) === strtolower($name)) {
                return new Quality($quality['id']);
            }
        }
        throw new RuntimeException(sprintf(
            'Quality name "%s" was not found',
            $name
        ));
    }
}
