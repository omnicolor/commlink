<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/Modules',
        __DIR__ . '/app',
        __DIR__ . '/bootstrap',
        __DIR__ . '/config',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ])
    ->withAttributesSets()
    ->withCodeQualityLevel(0)
    ->withComposerBased(phpunit: true)
    ->withDeadCodeLevel(0)
    ->withImportNames()
    ->withTypeCoverageLevel(0);
