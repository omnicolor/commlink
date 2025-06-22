<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\FuncCall\CountArrayToEmptyArrayComparisonRector;
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
    ->withImportNames(removeUnusedImports: true)
    ->withPreparedSets(
        deadCode: true,
    )
    ->withRules([
        CountArrayToEmptyArrayComparisonRector::class,
    ])
    ->withTypeCoverageLevel(0);
