<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Override;

/**
 * Create a stub for a new Roll object.
 * @codeCoverageIgnore
 */
class MakeRollCommand extends GeneratorCommand
{
    /** @var string */
    protected $signature = 'make:roll {name}';

    /** @var string */
    protected $description = 'Create a new server- and system-agnostic Roll';

    /** @var string */
    protected $type = 'Roll';

    protected function getStub(): string
    {
        return $this->laravel->basePath('/stubs/roll.stub');
    }

    #[Override]
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\Rolls';
    }
}
