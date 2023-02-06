<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * Knowledge skill a character possesses.
 * @property string $id
 * @property string $short_category
 */
class KnowledgeSkill extends Skill
{
    /**
     * Category of knowledge skill (professional, academic, etc).
     * @var string
     */
    public string $category;

    /**
     * Create a new Knowledge skill object.
     * @param string $name Name of the skill
     * @param string $category Category (professional, language, etc)
     * @param int|string $level Level for the skill
     * @param ?string $specializations Optional specializations
     * @throws \RuntimeException
     * @phpstan-ignore-next-line
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
            throw new \RuntimeException(\sprintf(
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

    public function id(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                return (string)preg_replace(
                    ['/ /', '/[^a-zA-Z0-9-]/'],
                    ['-', ''],
                    $this->name
                );
            },
        );
    }

    public function shortCategory(): Attribute
    {
        return Attribute::make(
            get: function () {
                // @phpstan-ignore-next-line
                return match ($this->category) {
                    'academic' => 'acad',
                    'interests' => 'int',
                    'language' => 'lang',
                    'professional' => 'prof',
                    'street' => 'str',
                };
            },
        );
    }
}
