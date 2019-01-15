<?php
declare(strict_types=1);

namespace Tests\Unit\Services;

use LaraTest\Traits\AccessProtectedTraits;
use LaraTest\Traits\MockTraits;
use LaraTest\Traits\RepositoryAssertTraits;
use phpmock\MockBuilder;
use Tests\TestCase;

/**
 * Class TestCaseService
 * @package Tests\Unit\Services
 */
abstract class TestCaseService extends TestCase
{
    use RepositoryAssertTraits, MockTraits, AccessProtectedTraits;

    /**
     * @param $class
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    public function getMockObjectForClass($class)
    {
        return $this->getMockBuilder($class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @param $namespace
     * @param $functionName
     * @param bool $value
     * @return \phpmock\Mock
     * @throws \phpmock\MockEnabledException
     */
    protected function mockGlobalFunction($namespace, $functionName, $value = true)
    {
        $builder = new MockBuilder();
        $builder->setNamespace($namespace);
        $builder->setName($functionName);
        $builder->setFunction(function () use ($value) {
            return $value;
        });
        $mock = $builder->build();
        $mock->enable();
        return $mock;
    }
}
