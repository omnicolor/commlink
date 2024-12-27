<?php

declare(strict_types=1);

namespace Modules\Expanse\Models;

use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

/**
 * Class for Expanse backgrounds.
 */
class Background implements Stringable
{
    /**
     * Ability change the background gives.
     */
    public string $ability;

    /**
     * Collection of all backgrounds.
     * @var ?array<string, array<string, string|int|array<int|string, int|string>>>
     */
    public static ?array $backgrounds = null;

    /**
     * Map of benefits that can be rolled for.
     * @var array<int, array<string, string|int>>
     */
    public array $benefits;

    /**
     * Description of the background.
     */
    public string $description;

    /**
     * Focuses the Background might give a character.
     * @var array<int, string>
     */
    public array $focuses;

    /**
     * Name of the background.
     */
    public string $name;

    /**
     * Page the background was shown on.
     */
    public int $page;

    /**
     * Collection of talents the background may confer.
     * @var array<int, string>
     */
    public array $talents;

    /**
     * @throws RuntimeException
     */
    public function __construct(public string $id)
    {
        $filename = config('expanse.data_path') . 'backgrounds.php';
        self::$backgrounds ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$backgrounds[$id])) {
            throw new RuntimeException(
                sprintf('Background ID "%s" is invalid', $id)
            );
        }

        $background = self::$backgrounds[$id];
        $this->ability = $background['ability'];
        $this->benefits = $background['benefits'];
        $this->description = $background['description'];
        $this->focuses = $background['focuses'];
        $this->id = $id;
        $this->name = $background['name'];
        $this->page = $background['page'];
        $this->talents = $background['talents'];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return an array of focuses the background may give the character.
     */
    public function getFocuses(): FocusArray
    {
        $focuses = new FocusArray();
        foreach ($this->focuses as $focus) {
            try {
                $focuses[] = new Focus($focus);
            } catch (RuntimeException) {
                // Ignore.
            }
        }
        return $focuses;
    }

    /**
     * Return an array of talents the background may give the character.
     */
    public function getTalents(): TalentArray
    {
        $talents = new TalentArray();
        foreach ($this->talents as $talent) {
            try {
                $talents[] = new Talent($talent);
            } catch (RuntimeException) {
                // Ignore.
            }
        }
        return $talents;
    }
}
