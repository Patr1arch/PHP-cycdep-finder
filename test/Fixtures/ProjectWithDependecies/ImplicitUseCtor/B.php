<?php

namespace Patriarch\PhpCycdepFinder\Fixtures\ProjectWithDependecies\ImplicitUseCtor;

class B
{
    public function __construct()
    {
        new C();
    }
}