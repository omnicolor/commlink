<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Nwidart\Modules\Facades\Module;
use Override;
use Stringable;
use Sushi\Sushi;

use function config;
use function file_exists;
use function sprintf;

/**
 * @method static self module(string $module_id)
 * @method static self required()
 * @phpstan-type RulesetArray array{
 *     description: string,
 *     id: string,
 *     isbn: string,
 *     name: string,
 *     required: bool
 * }
 * @property-read string $name
 */
class Ruleset extends Model implements Stringable
{
    use Sushi;

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'description',
        'id',
        'isbn',
        'name',
        'required',
    ];

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    #[Override]
    public function casts(): array
    {
        return [
            'required' => 'bool',
        ];
    }

    /**
     * @return array<int, RulesetArray>
     */
    public function getRows(): array
    {
        $rulesets = new Collection();
        foreach (Module::allEnabled() as $module) {
            $filename = config(sprintf('%s.data_path', $module->getLowerName()))
                . 'rulesets.php';
            if (file_exists($filename)) {
                /** @var array<int, RulesetArray> $ruleset */
                $ruleset = require $filename;
                $rulesets = $rulesets->concat($ruleset);
            }
        }

        return $rulesets->toArray();
    }

    #[Scope]
    protected function module(Builder $query, string $module): void
    {
        $query->where('id', 'like', $module . '%');
    }

    #[Scope]
    protected function required(Builder $query): void
    {
        $query->where('required', '=', true);
    }
}
