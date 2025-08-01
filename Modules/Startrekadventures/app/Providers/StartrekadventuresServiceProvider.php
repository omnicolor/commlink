<?php

declare(strict_types=1);

namespace Modules\Startrekadventures\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Override;

use function array_merge;
use function config;
use function config_path;
use function is_dir;
use function ltrim;
use function module_path;
use function resource_path;
use function str_replace;

/**
 * @codeCoverageIgnore
 */
class StartrekadventuresServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Startrekadventures';

    protected string $moduleNameLower = 'startrekadventures';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));
    }

    /**
     * Register the service provider.
     */
    #[Override]
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register commands in the format of Command::class.
     */
    protected function registerCommands(): void
    {
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'lang'), $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(module_path($this->moduleName, 'lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $this->publishes(
            [module_path($this->moduleName, 'config/config.php') => config_path($this->moduleNameLower . '.php')],
            'config',
        );
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'config/config.php'),
            $this->moduleNameLower,
        );
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);
        $sourcePath = module_path($this->moduleName, 'resources/views');

        $this->publishes(
            [$sourcePath => $viewPath],
            ['views', $this->moduleNameLower . '-module-views'],
        );

        $this->loadViewsFrom(
            array_merge($this->getPublishableViewPaths(), [$sourcePath]),
            $this->moduleNameLower,
        );

        $componentNamespace = str_replace(
            '/',
            '\\',
            config('modules.namespace') . '\\' . $this->moduleName . '\\'
                . ltrim(
                    config('modules.paths.generator.component-class.path'),
                    config('modules.paths.app_folder', '')
                )
        );
        Blade::componentNamespace($componentNamespace, $this->moduleNameLower);
    }

    /**
     * Get the services provided by the provider.
     * @return array<string>
     */
    #[Override]
    public function provides(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }

        return $paths;
    }
}
