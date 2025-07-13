<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->exclude('var')
    ->in('./api')
;

$config = new PhpCsFixer\Config();
return $config
    ->setRules([
        '@Symfony' => true,
        '@PHP84Migration' => true,
        '@DoctrineAnnotation' => true,
    ])
    ->setFinder($finder)
;
