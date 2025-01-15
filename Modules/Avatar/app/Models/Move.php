<?php

declare(strict_types=1);

namespace Modules\Avatar\Models;

use RuntimeException;
use Stringable;

use function array_keys;
use function sprintf;

class Move implements Stringable
{
    public string $description;
    public string $name;
    public int $page;
    public ?Playbook $playbook = null;
    public string $ruleset;

    /** @var ?array<string, mixed> */
    public static ?array $moves;

    public function __construct(public string $id)
    {
        $filename = config('avatar.data_path') . 'moves.php';
        self::$moves ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$moves[$id])) {
            throw new RuntimeException(
                sprintf('Move ID "%s" is invalid', $id)
            );
        }

        $move = self::$moves[$id];
        $this->description = $move['description'];
        $this->name = $move['name'];
        $this->page = $move['page'];
        $this->ruleset = $move['ruleset'];

        if (null !== $move['playbook']) {
            $this->playbook = new Playbook($move['playbook']);
        }
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array<string, self>
     */
    public static function all(): array
    {
        $filename = config('avatar.data_path') . 'moves.php';
        self::$moves ??= require $filename;

        $moves = [];
        /** @var string $id */
        foreach (array_keys(self::$moves) as $id) {
            $moves[$id] = new self($id);
        }
        return $moves;
    }
}
