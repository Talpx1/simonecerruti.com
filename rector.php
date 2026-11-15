<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Empty_\SimplifyEmptyCheckOnEmptyArrayRector;
use Rector\CodingStyle\Rector\PostInc\PostIncDecToPreIncDecRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPublicMethodParameterRector;
use Rector\EarlyReturn\Rector\If_\ChangeOrIfContinueToMultiContinueRector;
use Rector\EarlyReturn\Rector\Return_\ReturnBinaryOrToEarlyReturnRector;
use Rector\Php80\Rector\Switch_\ChangeSwitchToMatchRector;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use Rector\Php83\Rector\ClassConst\AddTypeToConstRector;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;
use Rector\TypeDeclaration\Rector\ArrowFunction\AddArrowFunctionReturnTypeRector;
use Rector\TypeDeclaration\Rector\BooleanAnd\BinaryOpNullableToInstanceofRector;
use Rector\TypeDeclaration\Rector\Closure\AddClosureVoidReturnTypeWhereNoReturnRector;
use Rector\TypeDeclaration\Rector\Closure\ClosureReturnTypeRector;
use RectorLaravel\Rector\FuncCall\ConfigToTypedConfigMethodCallRector;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/config',
        __DIR__.'/app',
        __DIR__.'/database',
        __DIR__.'/routes',
    ])
    ->withSets([
        LaravelSetList::LARAVEL_CODE_QUALITY,
        LaravelSetList::LARAVEL_COLLECTION,
        LaravelSetList::LARAVEL_130,
        LaravelSetList::LARAVEL_IF_HELPERS,
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        privatization: true,
        instanceOf: true,
        earlyReturn: true,
        rectorPreset: true,
    )
    ->withRules([
        ChangeSwitchToMatchRector::class,
        ReadOnlyPropertyRector::class,
        AddTypeToConstRector::class,
        ConfigToTypedConfigMethodCallRector::class,
    ])
    ->withSkip([
        // Conflicts with Pint's increment_style (post-increment); Pint is the canonical formatter.
        PostIncDecToPreIncDecRector::class,
        SimplifyEmptyCheckOnEmptyArrayRector::class,
        DisallowedEmptyRuleFixerRector::class,
        AddArrowFunctionReturnTypeRector::class,
        AddClosureVoidReturnTypeWhereNoReturnRector::class,
        ClosureReturnTypeRector::class,
        BinaryOpNullableToInstanceofRector::class,
        ChangeOrIfContinueToMultiContinueRector::class,
        RemoveUnusedPublicMethodParameterRector::class => [
            __DIR__.'/app/Policies',
        ],
        ReturnBinaryOrToEarlyReturnRector::class => [
            __DIR__.'/app/Providers/TelescopeServiceProvider.php',
        ],
    ]);
