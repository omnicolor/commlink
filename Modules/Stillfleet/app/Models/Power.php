<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Models;

use Override;
use RuntimeException;
use Stringable;

use function array_keys;
use function config;
use function sprintf;
use function strtolower;

/**
 * A power granted to a character by their class or species, or achieved through
 * leveling up.
 */
class Power implements Stringable
{
    public const string TYPE_ADVANCED = 'advanced';
    public const string TYPE_CLASS = 'class';
    public const string TYPE_HELL_SCIENCE = 'hell-science';
    public const string TYPE_MARQUEE = 'marquee';
    public const string TYPE_SPECIES = 'species';

    public ?string $advanced_list;
    public string $description;
    public string $id;
    public string $name;
    public int $page;
    public string $ruleset;
    public string $type;

    /** @var ?array<string, array<string, int|string>> */
    protected static ?array $powers;

    public function __construct(string $id)
    {
        $filename = config('stillfleet.data_path') . 'powers.php';
        self::$powers ??= require $filename;

        $this->id = strtolower($id);
        if (!isset(self::$powers[$this->id])) {
            throw new RuntimeException(sprintf(
                'Power ID "%s" is invalid',
                $id
            ));
        }

        $power = self::$powers[$this->id];
        $this->advanced_list = $power['advanced-list'] ?? null;
        $this->description = $power['description'];
        $this->name = $power['name'];
        $this->page = $power['page'];
        $this->ruleset = $power['ruleset'];
        $this->type = $power['type'];
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array<int, Power>
     */
    public static function all(): array
    {
        $filename = config('stillfleet.data_path') . 'powers.php';
        self::$powers ??= require $filename;

        $powers = [];
        /** @var string $id */
        foreach (array_keys(self::$powers ?? []) as $id) {
            $powers[] = new Power($id);
        }
        return $powers;
    }
}
