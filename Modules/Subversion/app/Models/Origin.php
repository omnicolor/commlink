<?php

declare(strict_types=1);

namespace Modules\Subversion\Models;

use RuntimeException;

use function sprintf;

class Origin
{
    public string $description;
    public string $more;
    public string $name;
    public int $page;
    public string $ruleset;

    /**
     * @var array<string, array<string, int|string>>
     */
    public static ?array $origins;

    public function __construct(public string $id)
    {
        $filename = config('subversion.data_path') . 'origins.php';
        self::$origins ??= require $filename;

        if (!isset(self::$origins[$id])) {
            throw new RuntimeException(sprintf('Origin "%s" not found', $id));
        }

        $origin = self::$origins[$id];
        $this->description = $origin['description'];
        $this->more = $origin['more'];
        $this->name = $origin['name'];
        $this->page = $origin['page'];
        $this->ruleset = $origin['ruleset'];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array<int, Origin>
     */
    public static function all(): array
    {
        $filename = config('subversion.data_path') . 'origins.php';
        self::$origins ??= require $filename;

        $origins = [];
        foreach (self::$origins as $origin) {
            $origins[] = new Origin($origin['id']);
        }
        return $origins;
    }
}
