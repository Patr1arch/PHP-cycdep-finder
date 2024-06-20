<?php

namespace Patriarch\PhpCycdepFinder\Fixtures\ProjectWithoutDependecies\FatalCase;

class FatalCase
{

}

class Foo
{
    public function __construct()
    {
        $fatalCase = 'FatalCase';
        $bum = new $fatalCase();
    }
}
