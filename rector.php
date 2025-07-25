<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\If_\SimplifyIfElseToTernaryRector;
use Rector\CodingStyle\Rector\FuncCall\CountArrayToEmptyArrayComparisonRector;
use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\CodingStyle\Rector\Stmt\RemoveUselessAliasInUseStatementRector;
use Rector\Config\RectorConfig;
use Rector\Php53\Rector\FuncCall\DirNameFileConstantToDirConstantRector;
use Rector\Php70\Rector\FunctionLike\ExceptionHandlerTypehintRector;
use Rector\Php70\Rector\MethodCall\ThisCallOnStaticMethodToStaticCallRector;
use Rector\Php71\Rector\TryCatch\MultiExceptionCatchRector;
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
    ->withCodeQualityLevel(30)
    ->withCodingStyleLevel(5)
    ->withComposerBased(symfony: true, phpunit: true)
    ->withImportNames(removeUnusedImports: true)
    ->withPreparedSets(deadCode: true)
    ->withTypeCoverageLevel(34)
    ->withRules([
        AddOverrideAttributeToOverriddenMethodsRector::class,
        AddTypeToConstRector::class,
        ArrayKeyFirstLastRector::class,
        CountArrayToEmptyArrayComparisonRector::class,
        DirNameFileConstantToDirConstantRector::class,
        ExceptionHandlerTypehintRector::class,
        ExplicitNullableParamTypeRector::class,
        MultiExceptionCatchRector::class,
        ReadOnlyAnonymousClassRector::class,
        ReadOnlyPropertyRector::class,
        RemoveUselessAliasInUseStatementRector::class,
        RoundingModeEnumRector::class,
        ThisCallOnStaticMethodToStaticCallRector::class,
    ])
    ->withSkip([
        NewlineAfterStatementRector::class,
        SimplifyIfElseToTernaryRector::class,
    ]);
