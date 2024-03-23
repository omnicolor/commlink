<?php

declare(strict_types=1);

namespace App\Models\Subversion;

use RuntimeException;

use function sprintf;

class Background
{
    public string $description;
    public string $name;
    public int $page;
    public string $ruleset;

    /**
     * @var array<string, array<string, int|string>>
     */
    public static ?array $backgrounds;

    public function __construct(public string $id)
    {
        $filename = config('app.data_path.subversion') . 'backgrounds.php';
        self::$backgrounds ??= require $filename;

        if (!isset(self::$backgrounds[$id])) {
            throw new RuntimeException(sprintf('Background "%s" not found', $id));
        }

        $background = self::$backgrounds[$id];
        $this->description = $background['description'];
        $this->name = $background['name'];
        $this->page = $background['page'];
        $this->ruleset = $background['ruleset'];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<int, Background>
     */
    public static function all(): array
    {
        $filename = config('app.data_path.subversion') . 'backgrounds.php';
        self::$backgrounds ??= require $filename;

        $backgrounds = [];
        foreach (self::$backgrounds as $background) {
            $backgrounds[] = new Background($background['id']);
        }
        return $backgrounds;
    }
}
