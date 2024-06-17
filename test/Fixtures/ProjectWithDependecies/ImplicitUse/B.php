<?php

namespace Patriarch\PhpCycdepFinder\Fixtures\ProjectWithDependecies\ImplicitUse;

class B
{
    public static function bar()
    {
        A::foo();
        \Patriarch\PhpCycdepFinder\Fixtures\ProjectWithDependecies\ImplicitUse\OtherDir\C::baz();
    }
}