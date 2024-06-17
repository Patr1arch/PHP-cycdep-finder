<?php

namespace Patriarch\PhpCycdepFinder\Core\Model;

class DependencyNode
{
    /**
     * @param string $name
     * @param array<string> $dependencies
     * @param Color $color
     */
    public function __construct(
        public readonly string $name,
        public array $dependencies = [],
        public Color $color = Color::WHITE
    ) {
    }

    public function addDependency(string $to): void
    {
        $this->dependencies[] = $to;
    }

    public function __toString(): string
    {
        $res = "$this->name ->";
        foreach ($this->dependencies as $dependency) {
            $res .= " $dependency ";
        }

        return $res;
    }
}