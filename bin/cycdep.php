#!/usr/bin/env php

<?php

use Patriarch\PhpCycdepFinder\Core\Builder\PhpDependencyTreeBuilder;
use Patriarch\PhpCycdepFinder\Core\Finder\CyclicDependenciesFinder;

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $dependencyTree = (new PhpDependencyTreeBuilder(parseArgv($argv)))->buildDependencyTree();
    $finder = new CyclicDependenciesFinder($dependencyTree);
    echo $finder->getMessages() . PHP_EOL;
    return $finder->hasCyclicDependencies();
} catch (Error $error) {
    echo "Parse error: {$error->getMessage()}\n";
    return 255;
}

/**
 * @param array<string> $argv files, classes, directories
 *
 * @return array<string> php files
 */
function parseArgv(array $argv): array
{
    $fileNames = [];
    unset($argv[0]);
    foreach ($argv as $arg) {
        if (is_dir($arg)) {
            $dirIterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    $arg,
                    RecursiveDirectoryIterator::SKIP_DOTS
                )
            );
            foreach ($dirIterator as $file) {
                $fileNames[] = $file->getPathname();
            }
        } elseif (is_file($arg)) {
            $fileNames[] = $arg;
        }
    }

    return array_unique($fileNames);
}
