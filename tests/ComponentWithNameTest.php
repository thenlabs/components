<?php

use NubecuLabs\Components\ComponentWithEventsInterface;
use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\CompositeComponentWithEventsInterface;
use NubecuLabs\Components\CompositeComponentInterface;
use NubecuLabs\Components\ComponentWithNameInterface;
use NubecuLabs\Components\ComponentTrait;
use NubecuLabs\Components\CompositeComponentWithEventsTrait;
use NubecuLabs\Components\ComponentWithEventsTrait;
use NubecuLabs\Components\CompositeComponentTrait;
use NubecuLabs\Components\ComponentWithNameTrait;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('ComponentWithNameTest.php', function () {
    createMacro('tests', function () {
        test('$component->getName() === null', function () {
            $this->assertNull($this->component->getName());
        });

        $name = uniqid('comp');
        testCase("\$component->setName('$name')", function () use ($name) {
            setUp(function () use ($name) {
                $this->component->setName($name);
            });

            test("\$component->getName() === '$name'", function () use ($name) {
                $this->assertEquals($name, $this->component->getName());
            });
        });
    });

    testCase('exists an instance of ComponentInterface', function () {
        setUp(function () {
            $this->component = new class implements ComponentInterface, ComponentWithNameInterface {
                use ComponentTrait, ComponentWithNameTrait;
            };
        });

        useMacro('tests');
    });

    testCase('exists an instance of ComponentWithEventsInterface', function () {
        setUp(function () {
            $this->component = new class implements ComponentWithEventsInterface, ComponentWithNameInterface {
                use ComponentWithEventsTrait, ComponentWithNameTrait;
            };
        });

        useMacro('tests');
    });

    testCase('exists an instance of CompositeComponentInterface', function () {
        setUp(function () {
            $this->component = new class implements CompositeComponentInterface, ComponentWithNameInterface {
                use CompositeComponentTrait, ComponentWithNameTrait;
            };
        });

        useMacro('tests');
    });

    testCase('exists an instance of CompositeComponentWithEventsInterface', function () {
        setUp(function () {
            $this->component = new class implements CompositeComponentWithEventsInterface, ComponentWithNameInterface {
                use CompositeComponentWithEventsTrait, ComponentWithNameTrait;
            };
        });

        useMacro('tests');
    });
});
