<?php

namespace Patriarch\PhpCycdepFinder\Core\Model;

class DependencyNode
{
    /** @var string */
    public $name;

    /** @var array<string> */
    public $dependencies = [];

    public $color = Color::WHITE;

    /**
     * @param string $name
     * @param array<string> $dependencies
     * @param int $color
     */
    public function __construct(
        string $name,
        array $dependencies = [],
        int $color = Color::WHITE
    ) {
        $this->name = $name;
        $this->dependencies = $dependencies;
        $this->color = $color;
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