<?php

declare(strict_types=1);

namespace Modules\Avatar\Models;

use Override;
use RuntimeException;
use Stringable;

use function array_keys;
use function config;
use function sprintf;
use function strtolower;

class Status implements Stringable
{
    public const TYPE_NEGATIVE = 'negative';
    public const TYPE_POSITIVE = 'positive';

    public string $description;
    public string $effect;
    public string $name;
    public int $page;
    public string $ruleset;
    public string $short_description;
    public string $type;

    /**
     * List of all statuses.
     * @var array<string, int|string>
     */
    public static ?array $statuses;

    public function __construct(public string $id)
    {
        $filename = config('avatar.data_path') . 'statuses.php';
        self::$statuses ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$statuses[$id])) {
            throw new RuntimeException(
                sprintf('Status ID "%s" is invalid', $id)
            );
        }

        $status = self::$statuses[$id];
        $this->description = $status['description-long'];
        $this->effect = $status['effect'];
        $this->name = $status['name'];
        $this->page = $status['page'];
        $this->ruleset = $status['ruleset'];
        $this->short_description = $status['description-short'];
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array<int, Status>
     */
    public static function all(): array
    {
        $filename = config('avatar.data_path') . 'statuses.php';
        self::$statuses ??= require $filename;

        $statuses = [];
        foreach (array_keys(self::$statuses ?? []) as $status) {
            $statuses[] = new self((string)$status);
        }
        return $statuses;
    }
}
