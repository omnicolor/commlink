<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use App\Traits\FormulaConverter;
use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

/**
 * Technomancer complex form.
 */
class ComplexForm implements Stringable
{
    use FormulaConverter;

    /**
     * Description of the complex form.
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
     */
    public string $duration;

    /**
     * Fade formula for the complex form.
     */
    public string $fade;

    /**
     * Name of the form.
     */
    public string $name;

    /**
     * Page the form is described on.
     */
    public int $page;

    /**
     * Ruleset the form is introduced in.
     */
    public string $ruleset;

    /**
     * Optional Stream the form belongs to.
     */
    public ?string $stream;

    /**
     * What the form targets.
     */
    public string $target;

    /**
     * List of all forms.
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $forms;

    /**
     * @param ?int $level Optional level to assign to the form
     * @throws RuntimeException if the ID is not found
     */
    public function __construct(
        public string $identifier,
        public ?int $level = null,
    ) {
        $filename = config('shadowrun5e.data_path') . 'complex-forms.php';
        self::$forms ??= require $filename;

        $identifier = strtolower($identifier);
        if (!isset(self::$forms[$identifier])) {
            throw new RuntimeException(sprintf(
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

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Set the complex form's level.
     */
    public function setLevel(int $level): ComplexForm
    {
        $this->level = $level;
        return $this;
    }

    /**
     * Return the fade value for the spell, based on its level.
     * @throws RuntimeException If the level isn't set.
     */
    public function getFade(): int
    {
        if (!isset($this->level)) {
            throw new RuntimeException('Level has not been set');
        }
        return self::convertFormula($this->fade, 'L', $this->level);
    }
}
