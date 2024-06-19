<?php

namespace Patriarch\PhpCycdepFinder\Command;

use PHPUnit\Framework\TestCase;

class BinTest extends TestCase
{
    private const __PROJECT_DIRECTORY__ = __DIR__ . '/../../';
    private const __TEST_DIRECTORY__ = __DIR__ . '/../';

    public function testFindNoDependecies()
    {
        $this->assertEquals(0, $this->requireBinFile('Fixtures/ProjectWithoutDependecies'));
    }

    public function testFindUseDependecies()
    {
        $this->assertEquals(1, $this->requireBinFile('Fixtures/ProjectWithDependecies/UseDependency'));
        $this->assertEquals(1, $this->requireBinFile('Fixtures/ProjectWithDependecies/GroupUseDependency'));
    }

    public function testFindImplicitDependencies()
    {
        $this->assertEquals(1, $this->requireBinFile('Fixtures/ProjectWithDependecies/ImplicitUse'));
    }

    public function testFindImplicitCtorDependencies()
    {
        $this->assertEquals(1, $this->requireBinFile('Fixtures/ProjectWithDependecies/ImplicitUseCtor'));
    }

    public function testFindRequiresAndIncludes()
    {
        $this->assertEquals(1, $this->requireBinFile('Fixtures/ProjectWithDependecies/RequiresAndIncludes'));
    }

    public function testFindComposerRequires()
    {
        $this->assertEquals(1, $this->requireBinFile('Fixtures/ProjectWithDependecies/ComposerConfigs'));
    }

    private function requireBinFile(string $path): int
    {
        $argv[1] = self::__TEST_DIRECTORY__ . $path;
        return require_once(self::__PROJECT_DIRECTORY__ . 'bin/cycdep.php');
    }
}
