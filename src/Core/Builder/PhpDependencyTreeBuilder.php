<?php

namespace Patriarch\PhpCycdepFinder\Core\Builder;

use Patriarch\PhpCycdepFinder\Core\Model\DependencyTree;
use PhpParser\Node;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;

class PhpDependencyTreeBuilder
{
    private DependencyTree $dependencyTree;

    /**
     * @param array<string> $fileNames
     */
    public function __construct(private readonly array $fileNames)
    {
        $this->dependencyTree = new DependencyTree();
    }

    public function buildDependencyTree(): DependencyTree
    {
        foreach ($this->fileNames as $fileName) {
            $parser = (new ParserFactory())->createForHostVersion();

            $ast = $parser->parse(file_get_contents($fileName));

            $this->createDependenciesForUses($ast);
            $this->createDependenciesForGroupUses($ast);
        }

        return $this->dependencyTree;
    }

    /**
     * @param array<Node\Stmt> $ast
     */
    private function createDependenciesForUses(array $ast): void
    {
        //$dumper = new NodeDumper;
        $nodeFinder = new NodeFinder;

        $namespaces = $nodeFinder->findInstanceOf($ast, Node\Stmt\Namespace_::class);
        foreach ($namespaces as $namespace) {
            $classes = $nodeFinder->findInstanceOf($namespace, Node\Stmt\Class_::class);
            $uses = $nodeFinder->findInstanceOf($namespaces, Node\Stmt\Use_::class);
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

    private function createDependenciesForGroupUses(array $ast)
    {
    }
}