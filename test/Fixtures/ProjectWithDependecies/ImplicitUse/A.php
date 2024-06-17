<?php

namespace Patriarch\PhpCycdepFinder\Fixtures\ProjectWithDependecies\ImplicitUse;

class A
{
    public static function foo()
    {
        B::bar();
        \Patriarch\PhpCycdepFinder\Fixtures\ProjectWithDependecies\ImplicitUse\OtherDir\C::baz();
    }
}