<?php

declare(strict_types=1);

namespace Modules\Battletech\Models;

use DomainException;
use Illuminate\Database\Eloquent\Casts\Attribute as EloquentAttribute;
use Illuminate\Database\Eloquent\Model;
use Modules\Battletech\Enums\ActionRating;
use Modules\Battletech\Enums\Attribute;
use Modules\Battletech\Enums\TrainingRating;
use Override;
use Stringable;
use Sushi\Sushi;

use function config;
use function json_decode;
use function sprintf;

/**
 * @property ActionRating $action_rating
 * @property array<int, Attribute> $attributes
 * @property string $description
 * @property string|null $sub_description
 * @property string $id
 * @property string $name
 * @property string|null $sub_name
 * @property int $page
 * @property string $quote
 * @property string $ruleset
 * @property int $target_number
 * @property TrainingRating $training_rating
 */
class Skill extends Model implements Stringable
{
    use Sushi;

    public $incrementing = false;
    protected $keyType = 'string';

    public int|null $level = null;
    public string|null $specialty = null;

    #[Override]
    public function __toString(): string
    {
        $name = $this->name;
        if (null !== $this->sub_name) {
            $name = sprintf('%s/%s', $name, $this->sub_name);
        }
        if (null !== $this->specialty) {
            return sprintf('%s (%s)', $name, $this->specialty);
        }
        return $name;
    }

    protected function actionRating(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: static function (string $rating): ActionRating {
                return ActionRating::from($rating);
            },
        );
    }

    protected function attributes(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (string $attributes): array {
                $attributes = json_decode($attributes, true);
                array_walk($attributes, function (string &$attribute): void {
                    $attribute = Attribute::from($attribute);
                });
                return $attributes;
            },
        );
    }

    public function getCostToRaise(Character $character): int
    {
        if (10 === $this->level) {
            throw new DomainException('Skills can not be raised past level 10');
        }

        $modifier = 10;
        foreach ($character->traits as $trait) {
            if ('fast-learner' === $trait->id) {
                $modifier = 8;
                break;
            }
            if ('slow-learner' === $trait->id) {
                $modifier = 12;
            }
        }
        if (null === $this->level) {
            return 2 * $modifier;
        }

        return ($this->level + 1) * $modifier;
    }

    /**
     * @return array{
     *     action_rating: string,
     *     attributes: string,
     *     description: string,
     *     id: string,
     *     name: string,
     *     page: int,
     *     quote: string,
     *     ruleset: string,
     *     sub_description: string|null,
     *     sub_name: string|null,
     *     target_number: int,
     *     training_rating: string
     * }
     */
    public function getRows(): array
    {
        $filename = config('battletech.data_path') . 'skills.php';
        return require $filename;
    }

    protected function trainingRating(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: static function (string $rating): TrainingRating {
                return TrainingRating::from($rating);
            },
        );
    }
}
