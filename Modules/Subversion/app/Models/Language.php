<?php

declare(strict_types=1);

namespace Modules\Subversion\Models;

use Override;
use RuntimeException;
use Stringable;

use function config;
use function sprintf;

class Language implements Stringable
{
    public string $description;
    public string $name;
    public int $page;
    public string $ruleset;

    /**
     * @var array<string, array<string, int|string>>
     */
    public static ?array $languages;

    public function __construct(public string $id)
    {
        $filename = config('subversion.data_path') . 'languages.php';
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

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array<int, Language>
     */
    public static function all(): array
    {
        $filename = config('subversion.data_path') . 'languages.php';
        self::$languages ??= require $filename;

        $languages = [];
        foreach (self::$languages ?? [] as $language) {
            $languages[] = new Language($language['id']);
        }
        return $languages;
    }
}
