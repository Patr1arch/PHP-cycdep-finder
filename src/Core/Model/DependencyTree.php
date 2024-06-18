<?php

namespace Patriarch\PhpCycdepFinder\Core\Model;

class DependencyTree
{
    /** @var array<DependencyNode> */
    private array $adjacencyList = [];

    public function addDependency(string $from, string $to): void
    {
        if (($node = $this->getDependencyNode($from)) === null) {
            $node = $this->createDependencyNode($from);
            $this->addToAdjacencyList($node);
        }

        $node->addDependency($to);
    }

    public function mergeTree(DependencyTree $anotherTree): void
    {
        $this->adjacencyList = array_merge($this->adjacencyList, $anotherTree->getAdjacencyList());
    }

    public function getAdjacencyList(): array
    {
        return $this->adjacencyList;
    }

    public function __toString(): string
    {
        $res = "";
        foreach ($this->adjacencyList as $node) {
            $res .= $node->__toString() . PHP_EOL;
        }

        return $res;
    }

    public function getDependencyNode(string $name): ?DependencyNode
    {
        $filteredNodes = array_filter(
            $this->adjacencyList,
            function (DependencyNode $dependencyNode) use ($name) {
                return $dependencyNode->name === $name;
            }
        );

        if (empty($filteredNodes)) {
            return null;
        }

        return current($filteredNodes);
    }

    private function createDependencyNode(string $dependencyNodeName): DependencyNode
    {
        return new DependencyNode($dependencyNodeName);
    }

    private function addToAdjacencyList(DependencyNode $node): void
    {
        $this->adjacencyList[] = $node;
    }
}