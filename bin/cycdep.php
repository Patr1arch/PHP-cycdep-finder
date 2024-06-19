#!/usr/bin/env php

<?php

use Patriarch\PhpCycdepFinder\Core\Builder\DependencyTreeBuilder;
use Patriarch\PhpCycdepFinder\Core\Finder\CyclicDependenciesFinder;
use Patriarch\PhpCycdepFinder\Core\Model\VerbosityLevel;

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $parsedData = parseArgv($argv);
    $verbosityLevel = getVerbosityLevel($parsedData['options']);

    $dependencyTreeBuilder = (new DependencyTreeBuilder($parsedData['file_names']));
    $dependencyTree = $dependencyTreeBuilder->buildDependencyTree();
    printMessages($dependencyTreeBuilder->getMessages(), $verbosityLevel);

    $finder = new CyclicDependenciesFinder($dependencyTree);
    printMessages($finder->getMessages(), $verbosityLevel);

    return $finder->hasCyclicDependencies();
} catch (Throwable $error) {
    $errorMessages = [VerbosityLevel::LEVEL_ONE->value => [], VerbosityLevel::LEVEL_TWO->value => []];
    $errorMessages[VerbosityLevel::LEVEL_ONE->value][] = "Parse error: {$error->getMessage()}\n";
    $errorMessages[VerbosityLevel::LEVEL_TWO->value][] = print_r($error->getTrace(), true);
    printMessages($errorMessages, $verbosityLevel ?? VerbosityLevel::LEVEL_NONE);

    return 255;
}

/**
 * @param array<string> $argv files, classes, directories
 *
 * @return array{'options': array<string>, 'file_names': array<string>} array of options and array of fileNames
 */
function parseArgv(array $argv): array
{
    $fileNames = [];
    $options = [];
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
        } elseif (str_starts_with($arg, '-')) {
            $options[] = $arg;
        }
    }

    return ['options' => $options, 'file_names' => array_unique($fileNames)];
}

function getVerbosityLevel(array $options): VerbosityLevel
{
    foreach ($options as $option) {
        $optionName = preg_replace('/-/', '', $option);
        if ($optionName === 'vv') {
            return VerbosityLevel::LEVEL_TWO;
        } elseif ($optionName === 'v') {
            return VerbosityLevel::LEVEL_ONE;
        }
    }

    return VerbosityLevel::LEVEL_NONE;
}


/** @param<VerbosityLevel, array<string>> $messages */
function printMessages(array $messages, VerbosityLevel $verbosityLevel): void
{
    switch ($verbosityLevel->value) {
        case VerbosityLevel::LEVEL_TWO->value:
            if (!empty($messages[VerbosityLevel::LEVEL_TWO->value])) {
                echo implode("\n", $messages[VerbosityLevel::LEVEL_TWO->value]);
            }
        case VerbosityLevel::LEVEL_ONE->value:
            if (!empty($messages[VerbosityLevel::LEVEL_ONE->value])) {
                echo implode("\n", $messages[VerbosityLevel::LEVEL_ONE->value]);
            }
            break;
        default:
            // Do nothing
            break;
    }
}
