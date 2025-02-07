<?php

declare(strict_types=1);

namespace Modules\Avatar\Features;

use Override;
use Stringable;

/**
 * @property string $description
 * @property string $id
 * @property string $name
 */
abstract class Feature implements Stringable
{
    #[Override]
    abstract public function __toString(): string;

    abstract public function description(): string;
}
