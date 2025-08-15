<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Override;
use RangeException;
use Stringable;
use Sushi\Sushi;

use function array_walk;
use function config;
use function json_decode;
use function max;
use function sprintf;

use const JSON_THROW_ON_ERROR;

/**
 * @property-read int|string $attack
 * @property-read int|string $data_processing
 * @property-read string $description
 * @property-read int|string $firewall
 * @property-read string $id
 * @property-read string $initiative
 * @property int|null $level
 * @property-read string $name
 * @property-read int $page
 * @property-read array<int, SpritePower> $powers
 * @property-read string $ruleset
 * @property-read array<int, ActiveSkill> $skills
 * @property-read int|string $sleaze
 */
class Sprite extends Model implements Stringable
{
    use Sushi;

    public $incrementing = false;
    protected $keyType = 'string';

    private int|null $level = null;

    /** @var list<string> */
    protected $fillable = [
        'attack',
        'data_processing',
        'description',
        'firewall',
        'id',
        'initiative',
        'name',
        'page',
        'powers',
        'ruleset',
        'skills',
        'sleaze',
    ];

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    protected function attack(): Attribute
    {
        return Attribute::make(
            get: function (int $attack): int|string {
                if (null !== $this->level) {
                    return max(1, $attack + $this->level);
                }
                return $this->levelCalculation($attack);
            },
        );
    }

    protected function dataProcessing(): Attribute
    {
        return Attribute::make(
            get: function (int $data_processing): int|string {
                if (null !== $this->level) {
                    return max(1, $data_processing + $this->level);
                }
                return $this->levelCalculation($data_processing);
            },
        );
    }

    protected function firewall(): Attribute
    {
        return Attribute::make(
            get: function (int $firewall): int|string {
                if (null !== $this->level) {
                    return max(1, $firewall + $this->level);
                }
                return $this->levelCalculation($firewall);
            },
        );
    }

    /**
     * @return array<int, array{
     *     attack: int,
     *     data_processing: int,
     *     description: string,
     *     firewall: int,
     *     id: string,
     *     initiative: int,
     *     name: string,
     *     page: int,
     *     powers: string,
     *     ruleset: string,
     *     skills: string,
     *     sleaze: int
     * }>
     */
    public function getRows(): array
    {
        $filename = config('shadowrun6e.data_path') . 'sprites.php';
        return require $filename;
    }

    protected function initiative(): Attribute
    {
        return Attribute::make(
            get: function (int $initiative): string {
                if (null !== $this->level) {
                    return sprintf('%d+4d6', $initiative + $this->level * 2);
                }

                return sprintf('(L*2)+%d+4d6', $initiative);
            },
        );
    }

    protected function level(): Attribute
    {
        return Attribute::make(
            set: function (int $level): int {
                if (1 > $level) {
                    throw new RangeException('Level must be a positive integer');
                }
                $this->level = $level;
                return $level;
            },
        );
    }

    private function levelCalculation(int $attribute): string
    {
        if (0 === $attribute) {
            return 'L';
        }

        return 'L+' . $attribute;
    }

    protected function powers(): Attribute
    {
        return Attribute::make(
            get: static function (string $powers): array {
                $powers = json_decode($powers, true, flags: JSON_THROW_ON_ERROR);
                array_walk($powers, static function (string &$power): void {
                    $power = SpritePower::findOrFail($power);
                });
                return $powers;
            },
        );
    }

    protected function skills(): Attribute
    {
        return Attribute::make(
            get: static function (string $skills): array {
                $skills = json_decode($skills, true, flags: JSON_THROW_ON_ERROR);
                array_walk($skills, static function (string &$skill): void {
                    $skill = ActiveSkill::findOrFail($skill);
                });
                return $skills;
            },
        );
    }

    protected function sleaze(): Attribute
    {
        return Attribute::make(
            get: function (int $sleaze): int|string {
                if (null !== $this->level) {
                    return max(1, $sleaze + $this->level);
                }
                return $this->levelCalculation($sleaze);
            },
        );
    }
}
