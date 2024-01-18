<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocLineSpanFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/examples',
        __DIR__ . '/src',
    ])

    // add a single rule
    ->withRules([
        NoUnusedImportsFixer::class,
    ])

    // add sets - group of rules
    ->withPreparedSets(
        arrays: true,
        namespaces: true,
        // spaces: true,
        docblocks: true,
        comments: true,
    )
    ->withConfiguredRule(OrderedImportsFixer::class, [
        'imports_order' => ['class', 'function', 'const'],
        'sort_algorithm' => 'alpha',
        'case_sensitive' => true,
    ])
    ->withSkip([
        PhpdocLineSpanFixer::class,
    ]);
