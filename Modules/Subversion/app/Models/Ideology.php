<?php

declare(strict_types=1);

namespace Modules\Subversion\Models;

use RuntimeException;

use function sprintf;

class Ideology
{
    public string $description;
    public string $name;
    public int $page;
    public string $ruleset;
    public string $value;

    /**
     * @var array<string, array<string, int|string>>
     */
    public static ?array $ideologies;

    public function __construct(public string $id)
    {
        $filename = config('subversion.data_path') . 'ideologies.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$ideologies ??= require $filename;

        if (!isset(self::$ideologies[$id])) {
            throw new RuntimeException(sprintf('Ideology "%s" not found', $id));
        }

        $ideology = self::$ideologies[$id];
        $this->description = $ideology['description'];
        $this->name = $ideology['name'];
        $this->page = $ideology['page'];
        $this->ruleset = $ideology['ruleset'];
        $this->value = $ideology['value'];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array<int, Ideology>
     */
    public static function all(): array
    {
        $filename = config('subversion.data_path') . 'ideologies.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$ideologies ??= require $filename;

        $ideologies = [];
        foreach (self::$ideologies as $ideology) {
            $ideologies[] = new Ideology($ideology['id']);
        }
        return $ideologies;
    }
}
