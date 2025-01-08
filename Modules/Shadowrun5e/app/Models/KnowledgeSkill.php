<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use RuntimeException;
use Stringable;

use function in_array;
use function sprintf;

/**
 * Knowledge skill a character possesses.
 * @property string $id
 * @property string $short_category
 */
final class KnowledgeSkill extends Skill implements Stringable
{
    /**
     * Category of knowledge skill (professional, academic, etc).
     */
    public string $category;

    /**
     * Create a new Knowledge skill object.
     * @throws RuntimeException
     */
    public function __construct(
        string $name,
        string $category,
        string | int $level,
        ?string $specializations = null
    ) {
        $categories = [
            'academic',
            'interests',
            'language',
            'professional',
            'street',
        ];
        $this->name = $name;
        if (!in_array($category, $categories, true)) {
            throw new RuntimeException(sprintf(
                'Knowledge skill category "%s" is invalid',
                $category
            ));
        }
        $this->category = $category;
        $this->level = $level;
        $this->limit = 'mental';
        $this->attribute = match ($category) {
            'academic', 'professional' => 'logic',
            'interests', 'language', 'street' => 'intuition',
        };
        $this->specialization = $specializations;
    }

    public function __get(string $name): mixed
    {
        return match ($name) {
            'id' => $this->id(),
            'short_category' => $this->shortCategory(),
            default => null,
        };
    }

    public function id(): string
    {
        return (string)preg_replace(
            ['/ /', '/[^a-zA-Z0-9-]/'],
            ['-', ''],
            $this->name
        );
    }

    public function shortCategory(): string
    {
        return match ($this->category) {
            'academic' => 'acad',
            'interests' => 'int',
            'language' => 'lang',
            'professional' => 'prof',
            'street' => 'str',
            default => 'str',
        };
    }
}
