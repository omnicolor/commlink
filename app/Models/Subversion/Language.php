<?php

declare(strict_types=1);

namespace App\Models\Subversion;

use RuntimeException;

use function sprintf;

/**
 * @psalm-suppress PossiblyUnusedProperty
 */
class Language
{
    public string $description;
    public string $name;
    public int $page;
    public string $ruleset;

    /**
     * @var array<string, array<string, int|string>>
     */
    public static ?array $languages;

    /**
     * @psalm-suppress PossiblyUnusedProperty
     */
    public function __construct(public string $id)
    {
        $filename = config('app.data_path.subversion') . 'languages.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$languages ??= require $filename;

        if (!isset(self::$languages[$id])) {
            throw new RuntimeException(sprintf('Language "%s" not found', $id));
        }

        $language = self::$languages[$id];
        $this->description = $language['description'];
        $this->name = $language['name'];
        $this->page = $language['page'];
        $this->ruleset = $language['ruleset'];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<int, Language>
     */
    public static function all(): array
    {
        $filename = config('app.data_path.subversion') . 'languages.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$languages ??= require $filename;

        $languages = [];
        foreach (self::$languages as $language) {
            $languages[] = new Language($language['id']);
        }
        return $languages;
    }
}
