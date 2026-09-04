<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\GeneratorCommand;
use Override;

/**
 * Create a stub for a new Roll object.
 * @codeCoverageIgnore
 */
#[Description('Create a new server- and system-agnostic Roll')]
#[Signature('make:roll {name}')]
class MakeRollCommand extends GeneratorCommand
{
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
