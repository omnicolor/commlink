<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Override;
use RangeException;
use Stringable;
use Sushi\Sushi;

use function config;

/**
 * @property-read int $agility
 * @property-read int $body
 * @property-read int $charisma
 * @property int $force
 * @property-read string $id
 * @property-read int $intuition
 * @property-read int $logic
 * @property-read string $name
 * @property-read int $page
 * @property-read int $reaction
 * @property-read string $ruleset
 * @property-read int $strength
 * @property-read int $willpower
 */
class Spirit extends Model implements Stringable
{
    use Sushi;

    private int $force;
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'agility',
        'body',
        'charisma',
        'id',
        'intuition',
        'logic',
        'name',
        'page',
        'reaction',
        'ruleset',
        'strength',
        'willpower',
    ];

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    protected function agility(): Attribute
    {
        return Attribute::make(
            get: function (int $agility): int|string {
                if (isset($this->force)) {
                    return max(1, $agility + $this->force);
                }
                return $this->forceCalculation($agility);
            },
        );
    }

    protected function body(): Attribute
    {
        return Attribute::make(
            get: function (int $body): int|string {
                if (isset($this->force)) {
                    return max(1, $body + $this->force);
                }
                return $this->forceCalculation($body);
            },
        );
    }

    protected function charisma(): Attribute
    {
        return Attribute::make(
            get: function (int $charisma): int|string {
                if (isset($this->force)) {
                    return max(1, $charisma + $this->force);
                }
                return $this->forceCalculation($charisma);
            },
        );
    }

    protected function force(): Attribute
    {
        return Attribute::make(
            get: function (int $force): int {
                return $force;
            },
            set: function (int $force): int {
                if (1 > $force) {
                    throw new RangeException('Force must be a positive integer');
                }
                $this->force = $force;
                return $force;
            },
        );
    }

    private function forceCalculation(int $attribute): string
    {
        if (0 === $attribute) {
            return 'F';
        }

        if (0 > $attribute) {
            return 'F' . $attribute;
        }

        return 'F+' . $attribute;
    }

    /**
     * @return array{
     *     agility: int,
     *     body: int,
     *     charisma: int,
     *     id: string,
     *     intuition: int,
     *     logic: int,
     *     name: string,
     *     page: int,
     *     reaction: int,
     *     ruleset: string,
     *     strength: int,
     *     willpower: int
     * }
     */
    public function getRows(): array
    {
        $filename = config('shadowrun6e.data_path') . 'spirits.php';
        return require $filename;
    }

    protected function intuition(): Attribute
    {
        return Attribute::make(
            get: function (int $intuition): int|string {
                if (isset($this->force)) {
                    return max(1, $intuition + $this->force);
                }
                return $this->forceCalculation($intuition);
            },
        );
    }

    protected function logic(): Attribute
    {
        return Attribute::make(
            get: function (int $logic): int|string {
                if (isset($this->force)) {
                    return max(1, $logic + $this->force);
                }
                return $this->forceCalculation($logic);
            },
        );
    }

    protected function reaction(): Attribute
    {
        return Attribute::make(
            get: function (int $reaction): int|string {
                if (isset($this->force)) {
                    return max(1, $reaction + $this->force);
                }
                return $this->forceCalculation($reaction);
            },
        );
    }

    protected function strength(): Attribute
    {
        return Attribute::make(
            get: function (int $strength): int|string {
                if (isset($this->force)) {
                    return max(1, $strength + $this->force);
                }
                return $this->forceCalculation($strength);
            },
        );
    }

    protected function willpower(): Attribute
    {
        return Attribute::make(
            get: function (int $willpower): int|string {
                if (isset($this->force)) {
                    return max(1, $willpower + $this->force);
                }
                return $this->forceCalculation($willpower);
            },
        );
    }
}
