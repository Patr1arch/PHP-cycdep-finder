<?php

namespace Patriarch\PhpCycdepFinder\Core\Builder;

use Patriarch\PhpCycdepFinder\Core\Model\DependencyTree;
use PhpParser\Node;
use PhpParser\NodeDumper;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;

class PhpDependencyTreeBuilder
{
    private DependencyTree $dependencyTree;
    private NodeFinder $nodeFinder;

    /**
     * @param array<string> $fileNames
     */
    public function __construct(private readonly array $fileNames)
    {
        $this->dependencyTree = new DependencyTree();
        $this->nodeFinder = new NodeFinder();
    }

    public function buildDependencyTree(): DependencyTree
    {
        foreach ($this->fileNames as $fileName) {
            $parser = (new ParserFactory())->createForHostVersion();

            $ast = $parser->parse(file_get_contents($fileName));

            $this->buildDependenciesForUses($ast);
            $this->buildDependenciesForGroupUses($ast);
        }

        return $this->dependencyTree;
    }

    /**
     * @param array<Node\Stmt> $ast
     */
    private function buildDependenciesForUses(array $ast): void
    {
        $namespaces = $this->nodeFinder->findInstanceOf($ast, Node\Stmt\Namespace_::class);
        foreach ($namespaces as $namespace) {
            $classes = $this->nodeFinder->findInstanceOf($namespace, Node\Stmt\Class_::class);
            $uses = $this->nodeFinder->findInstanceOf($namespaces, Node\Stmt\Use_::class);
            foreach ($classes as $class) {
                foreach ($uses as $use) {
                    foreach ($use->uses as $useItem) {
                        $this->dependencyTree->addDependency(
                            $namespace->name . '\\' . $class->name->name,
                            $useItem->name
                        );
                    }
                }
            }
        }
    }

    private function buildDependenciesForGroupUses(array $ast): void
    {
        $namespaces = $this->nodeFinder->findInstanceOf($ast, Node\Stmt\Namespace_::class);
        foreach ($namespaces as $namespace) {
            $classes = $this->nodeFinder->findInstanceOf($namespace, Node\Stmt\Class_::class);
            $groupUses = $this->nodeFinder->findInstanceOf($namespaces, Node\Stmt\GroupUse::class);
            foreach ($classes as $class) {
                foreach ($groupUses as $groupUse) {
                    foreach ($groupUse->uses as $groupUseItem) {
                        $this->dependencyTree->addDependency(
                            $namespace->name . '\\' . $class->name->name,
                            $groupUse->prefix . '\\' . $groupUseItem->name
                        );
                    }
                }
            }
        }
    }
}