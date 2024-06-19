<?php

namespace Patriarch\PhpCycdepFinder\Core\Builder;

use Patriarch\PhpCycdepFinder\Core\Model\DependencyTree;
use Patriarch\PhpCycdepFinder\Core\Model\VerbosityLevel;

interface BuilderInterface
{
    public function __construct(array $fileNames);
    public function buildDependencyTree(): DependencyTree;

    /** @return array<VerbosityLevel, array<string>> */
    public function getMessages(): array;
}