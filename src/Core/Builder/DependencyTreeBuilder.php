<?php

namespace Patriarch\PhpCycdepFinder\Core\Builder;

use Patriarch\PhpCycdepFinder\Core\Model\DependencyTree;
use Patriarch\PhpCycdepFinder\Core\Model\VerbosityLevel;

class DependencyTreeBuilder implements BuilderInterface
{
    private const PHP_EXTENSION = '.php';
    private const COMPOSER_FILE_NAME = 'composer.json';
    private const VENDOR_DIR = 'vendor';

    private array $phpFileNames = [];
    private array $composerFileNames = [];

    /** @var array<BuilderInterface> */
    private array $builders = [];

    /** @var array<VerbosityLevel, array<string>> */
    private array $messages = [VerbosityLevel::LEVEL_ONE->value => [], VerbosityLevel::LEVEL_TWO->value => []];

    private DependencyTree $dependencyTree;

    /** @param array<string> $fileNames */
    public function __construct(array $fileNames)
    {
        $this->dependencyTree = new DependencyTree();

        foreach ($fileNames as $fileName) {
            $this->addPhpFileNameIfNeeded($fileName);
            $this->addComposerFileNameIfNeeded($fileName);
        }

        $this->builders[] = new PhpDependencyTreeBuilder($this->phpFileNames);
        $this->builders[] = new ComposerDependencyTreeBuilder($this->composerFileNames);
    }

    public function buildDependencyTree(): DependencyTree
    {
        foreach ($this->builders as $builder) {
            $this->dependencyTree->mergeTree($builder->buildDependencyTree());

            $this->messages[VerbosityLevel::LEVEL_ONE->value] =
                array_merge($this->messages[VerbosityLevel::LEVEL_ONE->value], $builder->getMessages()[VerbosityLevel::LEVEL_ONE->value]);
            $this->messages[VerbosityLevel::LEVEL_TWO->value] =
                array_merge($this->messages[VerbosityLevel::LEVEL_TWO->value], $builder->getMessages()[VerbosityLevel::LEVEL_TWO->value]);
        }

        return $this->dependencyTree;
    }

    /** @return array<VerbosityLevel, array<string>> */
    public function getMessages(): array
    {
        return $this->messages;
    }

    private function addPhpFileNameIfNeeded(string $fileName): void
    {
        $paths = explode('/', $fileName);
        if (
            !in_array(self::VENDOR_DIR, $paths) &&
            str_contains(end($paths), self::PHP_EXTENSION)
        ) {
            $this->phpFileNames[] = $fileName;
        }
    }

    private function addComposerFileNameIfNeeded(string $fileName): void
    {
        $paths = explode('/', $fileName);
        if (
            in_array(self::VENDOR_DIR, $paths) &&
            str_contains(end($paths), self::COMPOSER_FILE_NAME)
        ) {
            $this->composerFileNames[] = $fileName;
        }
    }
}
