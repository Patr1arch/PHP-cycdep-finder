<?php

namespace Patriarch\PhpCycdepFinder\Core\Finder;

use Patriarch\PhpCycdepFinder\Core\Model\Color;
use Patriarch\PhpCycdepFinder\Core\Model\DependencyNode;
use Patriarch\PhpCycdepFinder\Core\Model\DependencyTree;
use Patriarch\PhpCycdepFinder\Core\Model\VerbosityLevel;

class CyclicDependenciesFinder
{
    /** @var array<VerbosityLevel, array<string>> */
    private array $messages = [VerbosityLevel::LEVEL_ONE->value => [], VerbosityLevel::LEVEL_TWO->value => []];
    private array $dependencyStack = [];

    private bool $hasCyclicDependencies = false;

    public function __construct(private readonly DependencyTree $dependencyTree)
    {
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
            array_unshift($this->messages[VerbosityLevel::LEVEL_ONE->value], "Find cyclic dependencies!");
            return $this->messages;
        }
        return [VerbosityLevel::LEVEL_ONE->value => ["It's no dependencies in this files"]];
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
        $this->messages[VerbosityLevel::LEVEL_ONE->value][] =
            implode(' -> ', $this->dependencyStack) . " -> $toNode->name!";
    }
}