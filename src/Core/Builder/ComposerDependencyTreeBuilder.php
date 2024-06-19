<?php

namespace Patriarch\PhpCycdepFinder\Core\Builder;

use Patriarch\PhpCycdepFinder\Core\Model\DependencyTree;
use Patriarch\PhpCycdepFinder\Core\Model\VerbosityLevel;

class ComposerDependencyTreeBuilder implements BuilderInterface
{
    private const REQUIRE_IDENTIFIER = 'require';
    private const NAME_IDENTIFIER = 'name';

    /** @var array<int, array<string>> */
    private $messages = [VerbosityLevel::LEVEL_ONE => [], VerbosityLevel::LEVEL_TWO => []];

    /** @var DependencyTree */
    private $dependencyTree;

    /** @var array<string> */
    private $fileNames;

    /** @param array<string> $fileNames */
    public function __construct(array $fileNames)
    {
        $this->fileNames = $fileNames;
        $this->dependencyTree = new DependencyTree();
    }

    public function buildDependencyTree(): DependencyTree
    {
        foreach ($this->fileNames as $fileName) {
            $composerJsonArray = json_decode(file_get_contents($fileName), true);

            $this->messages[VerbosityLevel::LEVEL_TWO][] = print_r($composerJsonArray, true);

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