<?php

declare(strict_types=1);

namespace Modules\Battletech\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Modules\Battletech\Enums\QualityType;
use Override;
use Stringable;
use Sushi\Sushi;

use function config;
use function json_decode;

/**
 * Traits a character can have. "Trait" is a reserved word, so we're calling
 * them "Quality".
 * @property int $cost
 * @property string $description
 * @property string $id
 * @property string $name
 * @property array<int, string> $opposes
 * @property int $page
 * @property string $quote
 * @property string $ruleset
 * @property array<int, QualityType> $types
 */
class Quality extends Model implements Stringable
{
    use Sushi;

    public $incrementing = false;
    protected $keyType = 'string';

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array{
     *     cost: int,
     *     description: string,
     *     id: string,
     *     name: string,
     *     opposes: string,
     *     page: int,
     *     quote: string,
     *     ruleset: string
     * }
     */
    public function getRows(): array
    {
        $filename = config('battletech.data_path') . 'qualities.php';
        return require $filename;
    }

    protected function opposes(): Attribute
    {
        return Attribute::make(
            get: function (string $value): array {
                return json_decode($value, true);
            },
        );
    }

    protected function types(): Attribute
    {
        return Attribute::make(
            get: function (string $value): array {
                $types = json_decode($value, true);
                array_walk($types, function (string &$type): void {
                    $type = QualityType::from($type);
                });
                return $types;
            },
        );
    }
}
