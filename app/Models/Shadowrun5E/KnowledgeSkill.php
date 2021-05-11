<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

/**
 * Knowledge skill a character possesses.
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
     */
    public function __construct(
        string $name,
        string $category,
        $level,
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
}
