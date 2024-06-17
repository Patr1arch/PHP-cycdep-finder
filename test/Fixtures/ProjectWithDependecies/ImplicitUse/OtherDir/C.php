<?php

namespace Patriarch\PhpCycdepFinder\Fixtures\ProjectWithDependecies\ImplicitUse\OtherDir;

class C
{
    public static function baz()
    {
        \Patriarch\PhpCycdepFinder\Fixtures\ProjectWithDependecies\ImplicitUse\A::foo();
        \Patriarch\PhpCycdepFinder\Fixtures\ProjectWithDependecies\ImplicitUse\B::bar();
    }
}