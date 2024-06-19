<?php

namespace Patriarch\PhpCycdepFinder\Fixtures\ProjectWithDependecies\ImplicitUseCtor;

class C
{
    public function __construct()
    {
        new \Patriarch\PhpCycdepFinder\Fixtures\ProjectWithDependecies\ImplicitUseCtor\A();
    }
}