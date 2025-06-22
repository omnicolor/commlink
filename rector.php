<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\FuncCall\CountArrayToEmptyArrayComparisonRector;
use Rector\CodingStyle\Rector\Stmt\RemoveUselessAliasInUseStatementRector;
use Rector\CodingStyle\Rector\Use_\SeparateMultiUseImportsRector;
use Rector\Config\RectorConfig;
use Rector\Php73\Rector\FuncCall\ArrayKeyFirstLastRector;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use Rector\Php83\Rector\ClassConst\AddTypeToConstRector;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\Php83\Rector\Class_\ReadOnlyAnonymousClassRector;
use Rector\Php84\Rector\FuncCall\RoundingModeEnumRector;
use Rector\Php84\Rector\Param\ExplicitNullableParamTypeRector;

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
        AddOverrideAttributeToOverriddenMethodsRector::class,
        AddTypeToConstRector::class,
        ArrayKeyFirstLastRector::class,
        CountArrayToEmptyArrayComparisonRector::class,
        ExplicitNullableParamTypeRector::class,
        ReadOnlyAnonymousClassRector::class,
        ReadOnlyPropertyRector::class,
        RemoveUselessAliasInUseStatementRector::class,
        RoundingModeEnumRector::class,
        SeparateMultiUseImportsRector::class,
    ])
    ->withTypeCoverageLevel(0);
