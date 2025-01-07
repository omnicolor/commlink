<?php

declare(strict_types=1);

namespace Modules\Expanse\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Expanse\Models\Character;
use Modules\Expanse\Models\Focus;

use function array_keys;
use function assert;
use function config;

/**
 * @extends Factory<Character>
 */
class CharacterFactory extends Factory
{
    /**
     * @var array<int, string>
     */
    protected static array $backgrounds;

    /**
     * The name of the factory's corresponding model.
     * @var class-string<Character>
     */
    protected $model = Character::class;

    /**
     * @var array<int, string>
     */
    protected array $origins = ['belter', 'earther', 'martian'];

    /**
     * @var array<int, string>
     */
    protected static ?array $socialClasses;

    protected function loadBackgrounds(): void
    {
        $filename = (string)config('expanse.data_path') . 'backgrounds.php';
        /**
         * @var array<string, array<string, string>>
         */
        $backgrounds = require $filename;
        self::$backgrounds = array_keys($backgrounds);
    }

    protected function loadSocialClasses(): void
    {
        $filename = (string)config('expanse.data_path') . 'social-classes.php';
        /**
         * @var array<string, array<string, string>>
         */
        $classes = require $filename;
        self::$socialClasses = array_keys($classes);
    }

    /**
     * Define the model's default state.
     * @return array<string, array<int, array<string, string>>|string>
     */
    public function definition(): array
    {
        if (!isset(self::$socialClasses)) {
            $this->loadBackgrounds();
            $this->loadSocialClasses();
        }
        assert(null !== self::$socialClasses);
        return [
            'background' => (string)$this->faker->randomElement(self::$backgrounds),
            'focuses' => [
                ['id' => $this->faker->randomElement(array_keys(Focus::all()))],
            ],
            'name' => $this->faker->name,
            'origin' => (string)$this->faker->randomElement($this->origins),
            'owner' => $this->faker->email,
            'socialClass' => (string)$this->faker->randomElement(self::$socialClasses),
            'system' => 'expanse',
        ];
    }
}
