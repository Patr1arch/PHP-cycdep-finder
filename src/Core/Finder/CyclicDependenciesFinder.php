<?php

namespace Patriarch\PhpCycdepFinder\Core\Finder;

use Patriarch\PhpCycdepFinder\Core\Model\Color;
use Patriarch\PhpCycdepFinder\Core\Model\DependencyNode;
use Patriarch\PhpCycdepFinder\Core\Model\DependencyTree;
use Patriarch\PhpCycdepFinder\Core\Model\VerbosityLevel;

class CyclicDependenciesFinder
{
    /** @var array<VerbosityLevel, array<string>> */
    private $messages = [VerbosityLevel::LEVEL_ONE => [], VerbosityLevel::LEVEL_TWO => []];

    /** @var array<string> */
    private $dependencyStack = [];

    private $hasCyclicDependencies = false;

    /** @var DependencyTree */
    private $dependencyTree;

    public function __construct(DependencyTree $dependencyTree)
    {
        $this->dependencyTree = $dependencyTree;
        $this->find();
    }

    public function hasCyclicDependencies(): bool
    {
        return $this->hasCyclicDependencies;
    }

    /** @return array<VerbosityLevel, array<string>> */
    public function getMessages(): array
    {
        if ($this->hasCyclicDependencies) {
            array_unshift($this->messages[VerbosityLevel::LEVEL_ONE], "Find cyclic dependencies!");
            return $this->messages;
        }
        return [VerbosityLevel::LEVEL_ONE => ["It's no dependencies in this files"]];
    }

    private function find(): void
    {
        foreach ($this->dependencyTree->getAdjacencyList() as $adjacencyColorNode) {
            if ($adjacencyColorNode->color === Color::WHITE) {
                $this->doDFS($adjacencyColorNode);
            }
        }
    }

    private function doDFS(DependencyNode $node): void
    {
        $node->color = Color::GREY;
        array_push($this->dependencyStack, $node->name);

        foreach ($node->dependencies as $dependencyName) {
            if (($dependencyNode = $this->dependencyTree->getDependencyNode($dependencyName)) !== null) {
                if ($dependencyNode->color === Color::GREY) {
                    $this->handleCyclicDependency($node, $dependencyNode);
                } elseif ($dependencyNode->color === Color::WHITE) {
                    $this->doDFS($dependencyNode);
                }
            }
        }

        $node->color = Color::BLACK;
        array_pop($this->dependencyStack);
    }

    private function handleCyclicDependency(DependencyNode $fromNode, DependencyNode $toNode): void
    {
        $this->hasCyclicDependencies = true;

        $cycle = [];
        $stackCopy = $this->dependencyStack;
        while (!empty($stackCopy) && ($nodeName = array_pop($stackCopy)) !== $toNode->name) {
            array_unshift($cycle, $nodeName);
        }

        $this->messages[VerbosityLevel::LEVEL_ONE][] =
            "$toNode->name -> " . implode(' -> ', $cycle) . " -> $toNode->name!";
    }
}