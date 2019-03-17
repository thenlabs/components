<?php

use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\Tests\Component;
use NubecuLabs\Components\Tests\CompositeComponent;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('CompositeComponentTest.php', function () {
    createMethod('getNewComponent', function () {
        return new CompositeComponent;
    });

    testCase(sprintf('$component = new \\%s;', CompositeComponent::class), function () {
        useMacro('common tests for ComponentTrait and CompositeComponentTrait');

        $id = uniqid();
        test("\$component->hasChild('$id') === false", function() use ($id) {
            $this->assertFalse($this->component->hasChild($id));
        });

        test(sprintf('$component->hasChild(new %s) === false', Component::class), function() use ($id) {
            $child = $this->createMock(ComponentInterface::class);

            $this->assertFalse($this->component->hasChild($child));
        });

        testCase('$component->getId();', function () {
            test('returns an unique string that starts with "compositecomponent_"', function () {
                $id = $this->component->getId();

                $this->assertGreaterThan(13, strlen($id));
                $this->assertStringStartsWith('compositecomponent_', $id);
            });
        });

        testCase(sprintf('$component->addChild($child = new %s);', Component::class), function () {
            setUp(function () {
                $this->child = new Component;
                $this->component->addChild($this->child);
            });

            test('$component->hasChild($child) === true', function() {
                $this->assertTrue($this->component->hasChild($this->child));
            });

            test('$component->hasChild($child->getId()) === true', function() {
                $this->assertTrue($this->component->hasChild($this->child->getId()));
            });

            test('$child->getParent() === $component', function() {
                $this->assertEquals($this->component, $this->child->getParent());
            });
        });
    });
});
