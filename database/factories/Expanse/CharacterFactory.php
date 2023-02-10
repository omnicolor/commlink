<?php

declare(strict_types=1);

namespace Database\Factories\Expanse;

use App\Models\Expanse\Character;
use Illuminate\Database\Eloquent\Factories\Factory;

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
     * @var string
     */
    protected $model = Character::class;

    /**
     * @var array<int, string>
     */
    protected array $origins = ['belter', 'earther', 'martian'];

    /**
     * @var array<int, string>
     */
    protected static array $socialClasses;

    public function __construct(...$args)
    {
        parent::__construct(...$args);
        if (!isset(self::$socialClasses)) {
            $this->loadBackgrounds();
            $this->loadSocialClasses();
        }
    }

    protected function loadBackgrounds(): void
    {
        $filename = config('app.data_path.expanse') . 'backgrounds.php';
        $backgrounds = require $filename;
        self::$backgrounds = array_keys($backgrounds);
    }

    protected function loadSocialClasses(): void
    {
        $filename = config('app.data_path.expanse') . 'social-classes.php';
        $classes = require $filename;
        self::$socialClasses = array_keys($classes);
    }

    /**
     * Define the model's default state.
     * @return array<string, string>
     */
    public function definition(): array
    {
        return [
            'background' => $this->faker->randomElement(self::$backgrounds),
            'name' => $this->faker->name,
            'origin' => $this->faker->randomElement($this->origins),
            'owner' => $this->faker->email,
            'socialClass' => $this->faker->randomElement(self::$socialClasses),
            'system' => 'expanse',
        ];
    }
}
