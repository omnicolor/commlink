<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Nwidart\Modules\Commands\Make\GeneratorCommand;
use Nwidart\Modules\Laravel\LaravelFileRepository;
use Nwidart\Modules\Support\Stub;
use Nwidart\Modules\Traits\ModuleCommandTrait;
use Override;
use Symfony\Component\Console\Input\InputArgument;

use function config;
use function ltrim;

use const E_ERROR;

/**
 * @codeCoverageIgnore
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

    #[Override]
    public function handle(): int
    {
        /**
         * @phpstan-ignore offsetAccess.nonOffsetAccessible
         */
        $this->modules = $this->laravel['modules'];

        if (E_ERROR === parent::handle()) {
            return E_ERROR;
        }

        return self::SUCCESS;
    }

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

    #[Override]
    public function getDefaultNamespace(): string
    {
        return config('modules.paths.generator.roll.namespace')
            ?? ltrim(
                config('modules.paths.generator.roll.path', 'Rolls'),
                config('modules.paths.app_folder', '')
            );
    }
}
