<?php

namespace Patriarch\PhpCycdepFinder\Core\Builder;

use Patriarch\PhpCycdepFinder\Core\Builder\BuilderInterface;
use Patriarch\PhpCycdepFinder\Core\Model\DependencyTree;

class DependencyTreeBuilder implements BuilderInterface
{
    private const PHP_EXTENSION = '.php';
    private const COMPOSER_FILE_NAME = 'composer.json';
    private const VENDOR_DIR = 'vendor';

    private array $phpFileNames = [];
    private array $composerFileNames = [];

    /** @var array<BuilderInterface> */
    private array $builders = [];

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
        }

        return $this->dependencyTree;
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
