<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Set\LaravelLevelSetList;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/app',
        __DIR__.'/tests',
    ])
    ->withPhpSets()
    ->withAttributesSets(symfony: true, doctrine: true)
    ->withSets([
        LaravelLevelSetList::UP_TO_LARAVEL_110,

        LaravelSetList::LARAVEL_CODE_QUALITY,
        LaravelSetList::LARAVEL_COLLECTION,
        /* LaravelSetList::LARAVEL_STATIC_TO_INJECTION, */
        LaravelSetList::LARAVEL_COLLECTION,
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        privatization: true,
        earlyReturn: true,
        strictBooleans: true,
    )
    ->withRules([
        \RectorLaravel\Rector\If_\AbortIfRector::class,
        \RectorLaravel\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector::class,
        \RectorLaravel\Rector\MethodCall\AssertStatusToAssertMethodRector::class,
        /* \RectorLaravel\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector::class, */
        \RectorLaravel\Rector\MethodCall\RedirectBackToBackHelperRector::class,
        \RectorLaravel\Rector\If_\ThrowIfRector::class,
        \RectorLaravel\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector::class,

        /* STRICT RETURN TYPES */
        \Rector\TypeDeclaration\Rector\Class_\ReturnTypeFromStrictTernaryRector::class,
        \Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictNewArrayRector::class,
        \Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictNativeCallRector::class,
        \Rector\TypeDeclaration\Rector\ClassMethod\NumericReturnTypeFromStrictReturnsRector::class,
        \Rector\TypeDeclaration\Rector\ClassMethod\NumericReturnTypeFromStrictScalarReturnsRector::class,
        \Rector\TypeDeclaration\Rector\ClassMethod\BoolReturnTypeFromBooleanStrictReturnsRector::class,
        \Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictTypedPropertyRector::class,
        \Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictFluentReturnRector::class,
        \Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictConstantReturnRector::class,
        \Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictTypedCallRector::class,
    ]);
