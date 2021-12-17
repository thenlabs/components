<?php

namespace ThenLabs\Components\Tests;

use ThenLabs\Components\DependencyInterface;
use ThenLabs\Components\EditableDependencyTrait;

setTestCaseClass(TestCase::class);

testCase('test-EditableDependencyTrait.php', function () {
    setUp(function () {
        $this->instance = new class implements DependencyInterface {
            use EditableDependencyTrait;
        };
    });

    test('testing getter and setter of the name property', function () {
        $name = uniqid();

        $this->instance->setName($name);

        $this->assertEquals($name, $this->instance->getName());
    });

    test('the version property is empty by default', function () {
        $this->assertEmpty($this->instance->getVersion());
    });

    test('the incompatible versions property is null by default', function () {
        $this->assertNull($this->instance->getIncompatibleVersions());
    });

    test('the included dependencies property is empty by default', function () {
        $this->assertEmpty($this->instance->getIncludedDependencies());
    });

    test('the dependencies property is empty by default', function () {
        $this->assertEmpty($this->instance->getDependencies());
    });

    test('testing getter and setter of the version property', function () {
        $version = uniqid();

        $this->instance->setVersion($version);

        $this->assertEquals($version, $this->instance->getVersion());
    });

    test('testing getter and setter of the incompatible versions property', function () {
        $incompatibleVersions = uniqid();

        $this->instance->setIncompatibleVersions($incompatibleVersions);

        $this->assertEquals($incompatibleVersions, $this->instance->getIncompatibleVersions());
    });

    test('testing getter and setter of the dependencies property', function () {
        $dependencies = range(1, 10);

        $this->instance->setDependencies($dependencies);

        $this->assertEquals($dependencies, $this->instance->getDependencies());
    });

    test('testing getter and setter of the included dependencies property', function () {
        $includedDependencies = range(1, 10);

        $this->instance->setIncludedDependencies($includedDependencies);

        $this->assertEquals($includedDependencies, $this->instance->getIncludedDependencies());
    });

    test('testing addDependency() method', function () {
        $dependency1 = $this->createMock(DependencyInterface::class);
        $dependency2 = $this->createMock(DependencyInterface::class);

        $this->instance->addDependency($dependency1);
        $this->instance->addDependency($dependency2);

        $dependencies = $this->instance->getDependencies();

        $this->assertSame($dependency1, $dependencies[0]);
        $this->assertSame($dependency2, $dependencies[1]);
    });

    test('testing addIncludedDependency() method', function () {
        $dependency1 = $this->createMock(DependencyInterface::class);
        $dependency2 = $this->createMock(DependencyInterface::class);

        $this->instance->addIncludedDependency($dependency1);
        $this->instance->addIncludedDependency($dependency2);

        $includedDependencies = $this->instance->getIncludedDependencies();

        $this->assertSame($dependency1, $includedDependencies[0]);
        $this->assertSame($dependency2, $includedDependencies[1]);
    });
});
