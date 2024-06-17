<?php

namespace Patriarch\PhpCycdepFinder\Core\Finder;

use Patriarch\PhpCycdepFinder\Core\Model\Color;
use Patriarch\PhpCycdepFinder\Core\Model\DependencyNode;
use Patriarch\PhpCycdepFinder\Core\Model\DependencyTree;

class CyclicDependenciesFinder
{
    /** @var array<string> */
    private array $messages;
    private bool $hasCyclicDependencies = false;

    public function __construct(private DependencyTree $dependencyTree)
    {
        $this->find();
    }

    public function hasCyclicDependencies(): bool
    {
        return $this->hasCyclicDependencies;
    }

    public function getMessages(): string
    {
        return empty($this->messages) ? $this->handleNoDependencies() : implode("\n", $this->messages);
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
    }

    private function handleCyclicDependency(DependencyNode $fromNode, DependencyNode $toNode): void
    {
        $this->hasCyclicDependencies = true;
        $this->messages[] = "Find cyclic dependency start from $fromNode->name to $toNode->name";
    }

    private function handleNoDependencies(): string
    {
        return "It's no dependencies in this files";
    }
}