<?php

namespace Patriarch\PhpCycdepFinder\Core\Builder;

use Patriarch\PhpCycdepFinder\Core\Model\DependencyTree;
use Patriarch\PhpCycdepFinder\Core\Model\VerbosityLevel;

class ComposerDependencyTreeBuilder implements BuilderInterface
{
    private const REQUIRE_IDENTIFIER = 'require';
    private const NAME_IDENTIFIER = 'name';

    /** @var array<VerbosityLevel, array<string>> */
    private array $messages = [VerbosityLevel::LEVEL_ONE->value => [], VerbosityLevel::LEVEL_TWO->value => []];

    private DependencyTree $dependencyTree;

    public function __construct(private readonly array $fileNames)
    {
        $this->dependencyTree = new DependencyTree();
    }

    public function buildDependencyTree(): DependencyTree
    {
        foreach ($this->fileNames as $fileName) {
            $composerJsonArray = json_decode(file_get_contents($fileName), true);

            $this->messages[VerbosityLevel::LEVEL_TWO->value][] = print_r($composerJsonArray, true);

            $this->buildDependenciesForRequires($composerJsonArray);
        }

        return $this->dependencyTree;
    }


    /** @return  array<VerbosityLevel, array<string>> */
    public function getMessages(): array
    {
        return $this->messages;
    }

    private function buildDependenciesForRequires(array $composerJsonArray): void
    {
        if (array_key_exists(self::REQUIRE_IDENTIFIER, $composerJsonArray)) {
            foreach ($composerJsonArray[self::REQUIRE_IDENTIFIER] as $dependencyLibName => $version) {
                $this->dependencyTree->addDependency(
                    $composerJsonArray[self::NAME_IDENTIFIER] ?? '',
                    $dependencyLibName
                );
            }
        }
    }
}