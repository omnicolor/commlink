<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Modules\Shadowrun6e\Enums\AdeptPowerActivation;
use Override;
use Stringable;
use Sushi\Sushi;

use function config;
use function json_decode;

use const JSON_THROW_ON_ERROR;

/**
 * @property-read string $activation
 * @property-read float $cost
 * @property-read string $description
 * @property-read array<int, string>|null $effects
 * @property-read string $id
 * @property-read string $name
 * @property-read int $page
 * @property-read string $ruleset
 */
class AdeptPower extends Model implements Stringable
{
    use Sushi;

    public $incrementing = false;
    protected $keyType = 'string';

    /** @var list<string> */
    protected $fillable = [
        'activation',
        'cost',
        'description',
        'effects',
        'id',
        'name',
        'page',
        'ruleset',
    ];

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    protected function effects(): Attribute
    {
        return Attribute::make(
            get: static function (string|null $effects): array|null {
                if (null === $effects) {
                    return null;
                }
                return json_decode($effects, true, flags: JSON_THROW_ON_ERROR);
            },
        );
    }

    /**
     * @return array{
     *     activation: AdeptPowerActivation,
     *     cost: float,
     *     description: string,
     *     effects: string|null,
     *     id: string,
     *     name: string,
     *     page: int,
     *     ruleset: string
     * }
     */
    public function getRows(): array
    {
        $filename = config('shadowrun6e.data_path') . 'adept-powers.php';
        return require $filename;
    }
}
