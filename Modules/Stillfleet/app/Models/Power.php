<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Modules\Stillfleet\Enums\AdvancedPowersCategory;
use Override;
use Stringable;
use Sushi\Sushi;

use function config;
use function json_decode;

/**
 * A power granted to a character by their class or species, or achieved
 * through leveling up.
 * @property AdvancedPowersCategory|null $advanced_list
 * @property string $description
 * @property array<string, mixed> $effects
 * @property string $id
 * @property string $name
 * @property int $page
 * @property string $ruleset
 * @property string $type
 */
class Power extends Model implements Stringable
{
    use Sushi;

    public const string TYPE_ADVANCED = 'advanced';
    public const string TYPE_CLASS = 'class';
    public const string TYPE_HELL_SCIENCE = 'hell-science';
    public const string TYPE_MARQUEE = 'marquee';
    public const string TYPE_SPECIES = 'species';

    public $incrementing = false;
    protected $keyType = 'string';

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    public function effects(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                if (null === $this->attributes['effects']) {
                    return [];
                }
                return json_decode($this->attributes['effects'], true);
            },
        );
    }

    /**
     * @return array{
     *     advanced_list: ?AdvancedPowersCategory,
     *     description: string,
     *     effects: ?string,
     *     id: string,
     *     name: string,
     *     page: int,
     *     ruleset: string,
     *     type: string
     * }
     */
    public function getRows(): array
    {
        $filename = config('stillfleet.data_path') . 'powers.php';
        return require $filename;
    }
}
