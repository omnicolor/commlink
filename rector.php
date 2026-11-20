<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Catch_\ThrowWithPreviousExceptionRector;
use Rector\CodeQuality\Rector\Isset_\IssetOnPropertyObjectToPropertyExistsRector;
use Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector;
use Rector\CodingStyle\Rector\ClassLike\NewlineBetweenClassLikeStmtsRector;
use Rector\CodingStyle\Rector\ClassMethod\NewlineBeforeNewAssignSetRector;
use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\CodingStyle\Rector\String_\SimplifyQuoteEscapeRector;
use Rector\Config\RectorConfig;
use Rector\DowngradePhp74\Rector\ArrowFunction\ArrowFunctionToAnonymousFunctionRector;
use Rector\PHPUnit\CodeQuality\Rector\ClassMethod\NoSetupWithParentCallOverrideRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitSelfCallRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php84\Rector\MethodCall\NewMethodCallWithoutParenthesesRector;
use RectorLaravel\Rector\ClassMethod\MigrateToSimplifiedAttributeRector;
use RectorLaravel\Rector\Class_\FillablePropertyToFillableAttributeRector;
use RectorLaravel\Rector\Class_\HiddenPropertyToHiddenAttributeRector;
use RectorLaravel\Rector\MethodCall\AssertSeeToAssertSeeHtmlRector;
use RectorLaravel\Rector\PropertyFetch\ReplaceFakerInstanceWithHelperRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/Modules',
        __DIR__ . '/app',
        __DIR__ . '/bootstrap',
        __DIR__ . '/config',
        __DIR__ . '/database',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ])
    ->withRootFiles()
    ->withSkip([
        __DIR__ . '/bootstrap/cache',
    ])
    ->withAttributesSets(all: true)
    ->withComposerBased(
        phpunit: true,
        symfony: true,
        laravel: true,
    )
    ->withImportNames()
    ->withPhpSets(php84: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        instanceOf: true,
        if: true,
        earlyReturn: true,
        carbon: true,
        rectorPreset: true,
        phpunitCodeQuality: true,
        phpunitNarrowAsserts: true,
        phpunitMockToStub: true,
        symfonyCodeQuality: true,
        symfonyConfigs: true,
    )
    ->withRules([
        ArrowFunctionToAnonymousFunctionRector::class,
        PreferPHPUnitSelfCallRector::class,
    ])
    ->withSkip([
        AssertSeeToAssertSeeHtmlRector::class,
        CatchExceptionNameMatchingTypeRector::class,
        ClosureToArrowFunctionRector::class,
        FillablePropertyToFillableAttributeRector::class,
        HiddenPropertyToHiddenAttributeRector::class,
        IssetOnPropertyObjectToPropertyExistsRector::class,
        // Breaks many of the attribute-related methods in RPG systems.
        MigrateToSimplifiedAttributeRector::class,
        NewMethodCallWithoutParenthesesRector::class,
        NewlineAfterStatementRector::class,
        NewlineBeforeNewAssignSetRector::class,
        NewlineBetweenClassLikeStmtsRector::class,
        NoSetupWithParentCallOverrideRector::class,
        PreferPHPUnitThisCallRector::class,
        ReplaceFakerInstanceWithHelperRector::class,
        SimplifyQuoteEscapeRector::class,
        ThrowWithPreviousExceptionRector::class,
    ]);
