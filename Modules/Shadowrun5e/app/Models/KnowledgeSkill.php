<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use RuntimeException;

/**
 * Knowledge skill a character possesses.
 * @property string $id
 * @property string $short_category
 */
class KnowledgeSkill extends Skill
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
        if (!\in_array($category, $categories, true)) {
            throw new RuntimeException(\sprintf(
                'Knowledge skill category "%s" is invalid',
                $category
            ));
        }
        $this->category = $category;
        $this->level = $level;
        $this->limit = 'mental';
        switch ($this->category) {
            case 'academic':
                $this->attribute = 'logic';
                break;
            case 'interests':
                $this->attribute = 'intuition';
                break;
            case 'language':
                $this->attribute = 'intuition';
                break;
            case 'professional':
                $this->attribute = 'logic';
                break;
            case 'street':
                $this->attribute = 'intuition';
                break;
        }
        $this->specialization = $specializations;
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
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
        // @phpstan-ignore-next-line
        return match ($this->category) {
            'academic' => 'acad',
            'interests' => 'int',
            'language' => 'lang',
            'professional' => 'prof',
            'street' => 'str',
        };
    }
}
