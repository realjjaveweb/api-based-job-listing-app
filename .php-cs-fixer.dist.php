<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/src')
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true) // required e.g. for declare_strict_types
    ->setRules([
        '@PSR12' => true,
        'declare_strict_types' => true,
        'strict_param' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_superfluous_phpdoc_tags' => true,
        'phpdoc_trim' => true,
        'single_quote' => true,
        'no_unused_imports' => true,
    ])
    ->setFinder($finder)
;