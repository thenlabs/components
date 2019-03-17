<?php

use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\CompositeComponentInterface;
use NubecuLabs\Components\Tests\CompositeComponent;

createMacro('$component->getParent() === null', function () {
    test('$component->getParent() === null', function() {
        $this->assertNull($this->component->getParent());
    });
});

createMacro('common tests for ComponentTrait and CompositeComponentTrait', function () {
    setUp(function () {
        $this->component = $this->getNewComponent();
    });

    useMacro('$component->getParent() === null');

    testCase('$component->getId()', function () {
        test('always returns the same value', function () {
            $id = $this->component->getId();

            $this->assertEquals($id, $this->component->getId());
            $this->assertEquals($id, $this->component->getId());
            $this->assertEquals($id, $this->component->getId());
        });
    });

    testCase('$component->setParent(null)', function () {
        test('$component->getParent() === null', function() {
            $this->component->setParent(null);

            $this->assertNull($this->component->getParent());
        });
    });

    testCase('$component->setParent($parent = new \NubecuLabs\Components\Tests\CompositeComponent)', function () {
        setUp(function () {
            $this->parent = new CompositeComponent;
            $this->component->setParent($this->parent);
        });

        test('$component->getParent() === $parent', function() {
            $this->assertEquals($this->parent, $this->component->getParent());
        });

        test('$parent->hasChild($component) === true', function() {
            $this->assertTrue($this->parent->hasChild($this->component));
        });

        testCase('$component->setParent(null)', function () {
            setUp(function () {
                $this->component->setParent(null);
            });

            useMacro('$component->getParent() === null');

            test('$parent->hasChild($component) === false', function() {
                $this->assertFalse($this->parent->hasChild($this->component));
            });
        });

        testCase('$component->setParent($parent2 = new \NubecuLabs\Components\Tests\CompositeComponent)', function () {
            setUp(function () {
                $this->parent2 = new CompositeComponent;
                $this->component->setParent($this->parent2);
            });

            test('$parent->hasChild($component) === false', function() {
                $this->assertFalse($this->parent->hasChild($this->component));
            });

            test('$parent2->hasChild($component) === true', function() {
                $this->assertTrue($this->parent2->hasChild($this->component));
            });

            test('$component->getParent() === $parent2', function() {
                $this->assertEquals($this->parent2, $this->component->getParent());
            });
        });
    });
});
