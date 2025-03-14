<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\Closure\ClosureReturnTypeRector;
use Rector\Naming\Rector\Assign\RenameVariableToMatchMethodCallReturnTypeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictNativeCallRector;
use RectorLaravel\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector;
use RectorLaravel\Rector\MethodCall\AssertStatusToAssertMethodRector;
use RectorLaravel\Rector\FuncCall\TypeHintTappableCallRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;
use Rector\TypeDeclaration\Rector\FunctionLike\AddParamTypeSplFixedArrayRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddParamTypeFromPropertyTypeRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/tests',
    ])
    ->withPhpSets()
    ->withAttributesSets(symfony: true, doctrine: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        privatization: true,
        earlyReturn: true,
        strictBooleans: true,
    )
    ->withRules([
        ClosureReturnTypeRector::class,
        RenameVariableToMatchMethodCallReturnTypeRector::class,
        ReturnTypeFromStrictNativeCallRector::class,
        AddGenericReturnTypeToRelationsRector::class,
        AssertStatusToAssertMethodRector::class,
        TypeHintTappableCallRector::class,
        TypedPropertyFromStrictConstructorRector::class,
        AddParamTypeSplFixedArrayRector::class,
        AddParamTypeFromPropertyTypeRector::class,
    ]);
    /* ->withTypeCoverageLevel(0) */
    /* ->withDeadCodeLevel(0) */
    /* ->withCodeQualityLevel(0); */
