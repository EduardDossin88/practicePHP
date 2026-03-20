<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/../src')
    ->append([
        __DIR__ . '/../public/index.php',
        __DIR__ . '/../public/search.php',
        __DIR__ . '/../bin/console.php',
    ]);

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@PSR12' => true,
    'array_syntax' => ['syntax' => 'short'],
    'no_unused_imports' => true,
    'ordered_imports' => ['sort_algorithm' => 'alpha'],
])->setFinder($finder);