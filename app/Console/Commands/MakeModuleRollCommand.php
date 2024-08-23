<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Nwidart\Modules\Commands\Make\GeneratorCommand;
use Nwidart\Modules\Laravel\LaravelFileRepository;
use Nwidart\Modules\Support\Stub;
use Nwidart\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;

use function config;
use function ltrim;

use const E_ERROR;

/**
 * @codeCoverageIgnore
 * @psalm-suppress UnusedClass
 */
class MakeModuleRollCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    protected LaravelFileRepository $modules;

    /** @var string */
    protected $argumentName = 'roll';

    /** @var string */
    protected $name = 'module:make-roll';

    /** @var string */
    protected $description = 'Create a new roll for the specified module.';

    public function handle(): int
    {
        if (E_ERROR === parent::handle()) {
            return E_ERROR;
        }

        /**
         * @psalm-suppress UndefinedInterfaceMethod
         * @phpstan-ignore offsetAccess.nonOffsetAccessible
         */
        $this->modules = $this->laravel['modules'];
        return self::SUCCESS;
    }

    /**
     * @return array<int, array<int, int|string>>
     */
    protected function getArguments(): array
    {
        return [
            ['roll', InputArgument::REQUIRED, 'The name of roll to be created.'],
            ['module', InputArgument::REQUIRED, 'The name of module for the roll.'],
        ];
    }

    protected function getTemplateContents(): mixed
    {
        $module = $this->modules->findOrFail($this->getModuleName());

        return (new Stub('/roll.stub', [
            'NAME' => $this->getRollName(),
            'NAMESPACE' => $this->getClassNamespace($module),
            'CLASS' => $this->getClass(),
            'LOWER_NAME' => $module->getLowerName(),
            'MODULE' => $this->getModuleName(),
            'STUDLY_NAME' => $module->getStudlyName(),
            'MODULE_NAMESPACE' => $this->modules->config('namespace'),
        ]))->render();
    }

    protected function getDestinationFilePath(): string
    {
        $path = $this->modules->getModulePath($this->getModuleName());

        return $path . 'app/Rolls/' . $this->getRollName() . '.php';
    }

    private function getRollName(): string
    {
        return Str::studly($this->argument('roll'));
    }

    public function getDefaultNamespace(): string
    {
        return config('modules.paths.generator.roll.namespace')
            ?? ltrim(
                config('modules.paths.generator.roll.path', 'Rolls'),
                config('modules.paths.app_folder', '')
            );
    }
}