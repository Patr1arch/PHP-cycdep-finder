<?php

namespace Patriarch\PhpCycdepFinder\Command;

use PHPUnit\Framework\TestCase;

class BinTest extends TestCase
{
    private const __PROJECT_DIRECTORY__ = __DIR__ . '/../../';
    private const __TEST_DIRECTORY__ = __DIR__ . '/../';

    public function testFindNoDependecies()
    {
        $this->assertFalse($this->requireBinFile('Fixtures/ProjectWithoutDependecies'));
    }

    public function testFindUseDependecies()
    {
        $this->assertTrue($this->requireBinFile('Fixtures/ProjectWithDependecies/UseDependency'));
        $this->assertTrue($this->requireBinFile('Fixtures/ProjectWithDependecies/GroupUseDependency'));
    }

    public function testFindImplicitDependecies()
    {
        $this->assertTrue($this->requireBinFile('Fixtures/ProjectWithDependecies/ImplicitUse'));
    }

    private function requireBinFile(string $path): bool
    {
        $argv[1] = self::__TEST_DIRECTORY__ . $path;
        return require_once(self::__PROJECT_DIRECTORY__ . 'bin/cycdep.php');
    }
}
