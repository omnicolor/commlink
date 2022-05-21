<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

/**
 * Technomancer complex form.
 */
class ComplexForm
{
    use ForceTrait;

    /**
     * Description of the complex form.
     * @var string
     */
    public string $description;

    /**
     * Duration of the complex form.
     *
     * Should be one of:
     *   E: Extended Test
     *   I: Immediate
     *   P: Permanent
     *   S: Sustained
     *   Varies: Special for Hyperthreading
     * @var string
     */
    public string $duration;

    /**
     * Fade formula for the complex form.
     * @var string
     */
    public string $fade;

    /**
     * Identifier for the form.
     * @var string
     */
    public string $identifier;

    /**
     * Name of the form.
     * @var string
     */
    public string $name;

    /**
     * Page the form is described on.
     * @var int
     */
    public int $page;

    /**
     * Ruleset the form is introduced in.
     * @var string
     */
    public string $ruleset;

    /**
     * Optional Stream the form belongs to.
     * @var ?string
     */
    public ?string $stream;

    /**
     * What the form targets.
     * @var string
     */
    public string $target;

    /**
     * List of all forms.
     * @var ?array<string, mixed>
     */
    public static ?array $forms;

    /**
     * Constructor.
     * @param string $identifier ID to load
     * @param ?int $level Optional level to assign to the form
     * @throws \RuntimeException if the ID is not found
     */
    public function __construct(string $identifier, public ?int $level = null)
    {
        $filename = config('app.data_path.shadowrun5e') . 'complex-forms.php';
        self::$forms ??= require $filename;

        $identifier = \strtolower($identifier);
        if (!isset(self::$forms[$identifier])) {
            throw new \RuntimeException(\sprintf(
                'Complex Form ID "%s" is invalid',
                $identifier
            ));
        }

        $form = self::$forms[$identifier];
        $this->description = $form['description'];
        $this->duration = $form['duration'];
        $this->fade = $form['fade'];
        $this->identifier = $form['id'];
        $this->name = $form['name'];
        $this->page = $form['page'];
        $this->ruleset = $form['ruleset'];
        $this->stream = $form['stream'] ?? null;
        $this->target = $form['target'];
    }

    /**
     * Return the complex form as a string.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Set the complex form's level.
     * @param int $level
     * @return ComplexForm
     */
    public function setLevel(int $level): ComplexForm
    {
        $this->level = $level;
        return $this;
    }

    /**
     * Return the fade value for the spell, based on its level.
     * @return int
     * @throws \RuntimeException If the level isn't set.
     */
    public function getFade(): int
    {
        if (!isset($this->level)) {
            throw new \RuntimeException('Level has not been set');
        }
        return $this->convertFormula($this->fade, 'L', $this->level);
    }
}
