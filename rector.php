<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\BooleanAnd\RepeatedAndNotEqualToNotInArrayRector;
use Rector\CodeQuality\Rector\BooleanOr\RepeatedOrEqualToInArrayRector;
use Rector\CodeQuality\Rector\If_\SimplifyIfElseToTernaryRector;
use Rector\CodeQuality\Rector\Isset_\IssetOnPropertyObjectToPropertyExistsRector;
use Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector;
use Rector\CodingStyle\Rector\ClassLike\NewlineBetweenClassLikeStmtsRector;
use Rector\CodingStyle\Rector\ClassMethod\MakeInheritedMethodVisibilitySameAsParentRector;
use Rector\CodingStyle\Rector\ClassMethod\NewlineBeforeNewAssignSetRector;
use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\CodingStyle\Rector\String_\SimplifyQuoteEscapeRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\Assign\RemoveUnusedVariableAssignRector;
use Rector\DeadCode\Rector\Expression\RemoveDeadStmtRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitSelfCallRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
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
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;
use RectorLaravel\Rector\ClassMethod\ScopeNamedClassMethodToScopeAttributedClassMethodRector;

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
    ->withImportNames(removeUnusedImports: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        instanceOf: true,
        earlyReturn: true,
        carbon: true,
        rectorPreset: true,
        phpunitCodeQuality: true,
        symfonyCodeQuality: true,
        symfonyConfigs: true,
    )
    ->withRules([
        AddOverrideAttributeToOverriddenMethodsRector::class,
        AddTypeToConstRector::class,
        ArrayKeyFirstLastRector::class,
        DirNameFileConstantToDirConstantRector::class,
        ExceptionHandlerTypehintRector::class,
        ExplicitNullableParamTypeRector::class,
        MultiExceptionCatchRector::class,
        PreferPHPUnitSelfCallRector::class,
        ReadOnlyAnonymousClassRector::class,
        ReadOnlyPropertyRector::class,
        RoundingModeEnumRector::class,
        ScopeNamedClassMethodToScopeAttributedClassMethodRector::class,
        ThisCallOnStaticMethodToStaticCallRector::class,
    ])
    ->withSkip([
        CatchExceptionNameMatchingTypeRector::class,
        // Adds strict_types grot to blade files in modules.
        DeclareStrictTypesRector::class,
        IssetOnPropertyObjectToPropertyExistsRector::class,
        MakeInheritedMethodVisibilitySameAsParentRector::class,
        NewlineAfterStatementRector::class,
        NewlineBeforeNewAssignSetRector::class,
        NewlineBetweenClassLikeStmtsRector::class,
        PreferPHPUnitThisCallRector::class,
        RemoveDeadStmtRector::class => [
            __DIR__ . '/Modules/Battletech/tests/Feature/Models/CharacterTest.php',
        ],
        RemoveUnusedVariableAssignRector::class => [
            __DIR__ . '/Modules/Battletech/tests/Feature/Models/CharacterTest.php',
        ],
        RepeatedAndNotEqualToNotInArrayRector::class,
        RepeatedOrEqualToInArrayRector::class,
        SimplifyIfElseToTernaryRector::class,
        SimplifyQuoteEscapeRector::class,
    ]);
