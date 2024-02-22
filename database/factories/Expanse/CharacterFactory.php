<?php

declare(strict_types=1);

namespace Database\Factories\Expanse;

use App\Models\Expanse\Character;
use App\Models\Expanse\Focus;
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
    protected static ?array $socialClasses;

    /**
     * @psalm-suppress MissingParamType
     */
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
        $filename = (string)config('app.data_path.expanse') . 'backgrounds.php';
        /** @var array<string, array<string, string>> */
        $backgrounds = require $filename;
        self::$backgrounds = array_keys($backgrounds);
    }

    protected function loadSocialClasses(): void
    {
        $filename = (string)config('app.data_path.expanse')
            . 'social-classes.php';
        /** @var array<string, array<string, string>> */
        $classes = require $filename;
        self::$socialClasses = array_keys($classes);
    }

    /**
     * Define the model's default state.
     * @return array<string, array<int, array<string, string>>|string>
     */
    public function definition(): array
    {
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
