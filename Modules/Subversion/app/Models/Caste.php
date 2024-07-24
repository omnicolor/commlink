<?php

declare(strict_types=1);

namespace Modules\Subversion\Models;

use RuntimeException;

use function sprintf;

class Caste
{
    public string $description;
    public int $fortune;
    public string $name;
    public int $page;
    public string $ruleset;

    /**
     * @var array<string, array<string, int|string>>
     */
    public static ?array $castes;

    public function __construct(public string $id)
    {
        $filename = config('subversion.data_path') . 'castes.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$castes ??= require $filename;

        if (!isset(self::$castes[$id])) {
            throw new RuntimeException(sprintf('Caste "%s" not found', $id));
        }

        $caste = self::$castes[$id];
        $this->description = $caste['description'];
        $this->fortune = $caste['fortune'];
        $this->name = $caste['name'];
        $this->page = $caste['page'];
        $this->ruleset = $caste['ruleset'];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array<int, Caste>
     */
    public static function all(): array
    {
        $filename = config('subversion.data_path') . 'castes.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$castes ??= require $filename;

        $castes = [];
        foreach (self::$castes as $caste) {
            $castes[] = new Caste($caste['id']);
        }
        return $castes;
    }
}
