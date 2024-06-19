<?php

namespace Patriarch\PhpCycdepFinder\Core\Builder;

use Patriarch\PhpCycdepFinder\Core\Model\DependencyTree;
use Patriarch\PhpCycdepFinder\Core\Model\VerbosityLevel;

class DependencyTreeBuilder implements BuilderInterface
{
    private const PHP_EXTENSION = '.php';
    private const COMPOSER_FILE_NAME = 'composer.json';
    private const VENDOR_DIR = 'vendor';

    /** @var array<string> */
    private $phpFileNames = [];

    /** @var array<string> */
    private $composerFileNames = [];

    /** @var array<BuilderInterface> */
    private $builders = [];

    /** @var array<int, array<string>> */
    private $messages = [VerbosityLevel::LEVEL_ONE => [], VerbosityLevel::LEVEL_TWO => []];

    /** @var DependencyTree  */
    private $dependencyTree;

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

            $this->messages[VerbosityLevel::LEVEL_ONE] =
                array_merge($this->messages[VerbosityLevel::LEVEL_ONE], $builder->getMessages()[VerbosityLevel::LEVEL_ONE]);
            $this->messages[VerbosityLevel::LEVEL_TWO] =
                array_merge($this->messages[VerbosityLevel::LEVEL_TWO], $builder->getMessages()[VerbosityLevel::LEVEL_TWO]);
        }

        return $this->dependencyTree;
    }

    /** @return array<int, array<string>> */
    public function getMessages(): array
    {
        return $this->messages;
    }

    private function addPhpFileNameIfNeeded(string $fileName): void
    {
        $paths = explode('/', $fileName);
        if (
            !in_array(self::VENDOR_DIR, $paths) &&
            strpos(end($paths), self::PHP_EXTENSION) !== false
        ) {
            $this->phpFileNames[] = $fileName;
        }
    }

    private function addComposerFileNameIfNeeded(string $fileName): void
    {
        $paths = explode('/', $fileName);
        if (
            in_array(self::VENDOR_DIR, $paths) &&
            strpos(end($paths), self::COMPOSER_FILE_NAME) !== false
        ) {
            $this->composerFileNames[] = $fileName;
        }
    }
}
