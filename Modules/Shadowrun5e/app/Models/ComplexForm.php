<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use App\Traits\FormulaConverter;
use Override;
use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

/**
 * Technomancer complex form.
 */
final class ComplexForm implements Stringable
{
    use FormulaConverter;

    public readonly string $description;

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
    public readonly string $duration;

    /**
     * Fade formula for the complex form.
     */
    public readonly string $fade;
    public readonly string $name;
    public readonly int $page;
    public readonly string $ruleset;

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
        public readonly string $id,
        public int|null $level = null,
    ) {
        $filename = config('shadowrun5e.data_path') . 'complex-forms.php';
        self::$forms ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$forms[$id])) {
            throw new RuntimeException(sprintf(
                'Complex Form ID "%s" is invalid',
                $id
            ));
        }

        $form = self::$forms[$id];
        $this->description = $form['description'];
        $this->duration = $form['duration'];
        $this->fade = $form['fade'];
        $this->name = $form['name'];
        $this->page = $form['page'];
        $this->ruleset = $form['ruleset'];
        $this->stream = $form['stream'] ?? null;
        $this->target = $form['target'];
    }

    #[Override]
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
