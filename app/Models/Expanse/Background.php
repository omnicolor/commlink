<?php

declare(strict_types=1);

namespace App\Models\Expanse;

/**
 * Class for Expanse backgrounds.
 */
class Background
{
    /**
     * Ability change the background gives.
     * @var string
     */
    public string $ability;

    /**
     * Collection of all backgrounds.
     * @var ?array<string, array<string, string|int|array<int|string, int|string>>>
     */
    public static ?array $backgrounds;

    /**
     * Map of benefits that can be rolled for.
     * @var array<int, array<string, string|int>>
     */
    public array $benefits;

    /**
     * Description of the background.
     * @var string
     */
    public string $description;

    /**
     * Focuses the Background might give a character.
     * @var array<int, string>
     */
    public array $focuses;

    /**
     * Unique ID for the background.
     * @var string
     */
    public string $id;

    /**
     * Name of the background.
     * @var string
     */
    public string $name;

    /**
     * Page the background was shown on.
     * @var int
     */
    public int $page;

    /**
     * Collection of talents the background may confer.
     * @var array<int, string>
     */
    public array $talents;

    /**
     * Constructor.
     * @param string $id
     * @throws \RuntimeException
     */
    public function __construct(string $id)
    {
        $filename = config('app.data_path.expanse') . 'backgrounds.php';
        self::$backgrounds ??= require $filename;

        $id = \strtolower($id);
        if (!isset(self::$backgrounds[$id])) {
            throw new \RuntimeException(
                \sprintf('Background ID "%s" is invalid', $id)
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

    /**
     * Return the name of the Background.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return an array of focuses the background may give the character.
     * @return FocusArray
     */
    public function getFocuses(): FocusArray
    {
        $focuses = new FocusArray();
        foreach ($this->focuses as $focus) {
            try {
                $focuses[] = new Focus($focus);
            } catch (\RuntimeException $ex) {
                // Ignore.
            }
        }
        return $focuses;
    }

    /**
     * Return an array of talents the background may give the character.
     * @return TalentArray
     */
    public function getTalents(): TalentArray
    {
        $talents = new TalentArray();
        foreach ($this->talents as $talent) {
            try {
                $talents[] = new Talent($talent);
            } catch (\RuntimeException $ex) {
                // Ignore.
            }
        }
        return $talents;
    }
}
