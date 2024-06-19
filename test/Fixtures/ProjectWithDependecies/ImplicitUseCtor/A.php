<?php

namespace Patriarch\PhpCycdepFinder\Fixtures\ProjectWithDependecies\ImplicitUseCtor;

class A
{
    public function __construct()
    {
        new B();
    }

}