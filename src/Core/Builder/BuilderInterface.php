<?php

namespace Patriarch\PhpCycdepFinder\Core\Builder;

use Patriarch\PhpCycdepFinder\Core\Model\DependencyTree;

interface BuilderInterface
{
    public function __construct(array $fileNames);
    public function buildDependencyTree(): DependencyTree;
}