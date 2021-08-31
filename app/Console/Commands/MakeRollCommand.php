<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

/**
 * Create a stub for a new Roll object.
 * @codeCoverageIgnore
 */
class MakeRollCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'make:roll {name}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Create a new server-agnostic Roll';

    /**
     * Type of the object to be created.
     * @var string
     */
    protected $type = 'Roll';

    protected function getStub(): string
    {
        return $this->laravel->basePath('/stubs/roll.stub');
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\Rolls';
    }
}
