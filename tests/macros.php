<?php

use NubecuLabs\Components\Tests\CompositeComponent;
use Symfony\Component\EventDispatcher\EventDispatcher;

createMacro('common tests for ComponentTrait and CompositeComponentTrait', function () {
    setUp(function () {
        $this->component = $this->getNewComponent();
    });

    test('$component->getParent() === null', function () {
        $this->assertNull($this->component->getParent());
    });

    testCase('$component->getId();', function () {
        test('always returns the same value', function () {
            $id = $this->component->getId();

            $this->assertEquals($id, $this->component->getId());
            $this->assertEquals($id, $this->component->getId());
            $this->assertEquals($id, $this->component->getId());
        });
    });

    testCase('$component->setParent(null);', function () {
        test('$component->getParent() === null', function () {
            $this->component->setParent(null);

            $this->assertNull($this->component->getParent());
        });
    });

    testCase('$component->setParent($parent = new \NubecuLabs\Components\Tests\CompositeComponent);', function () {
        setUp(function () {
            $this->parent = new CompositeComponent;
            $this->component->setParent($this->parent);
        });

        test('$component->getParent() === $parent', function () {
            $this->assertEquals($this->parent, $this->component->getParent());
        });

        test('$parent->hasChild($component) === true', function () {
            $this->assertTrue($this->parent->hasChild($this->component));
        });

        testCase('$component->setParent(null);', function () {
            setUp(function () {
                $this->component->setParent(null);
            });

            test('$component->getParent() === null', function () {
                $this->assertNull($this->component->getParent());
            });

            test('$parent->hasChild($component) === false', function () {
                $this->assertFalse($this->parent->hasChild($this->component));
            });
        });

        testCase('$component->setParent($parent2 = new \NubecuLabs\Components\Tests\CompositeComponent);', function () {
            setUp(function () {
                $this->parent2 = new CompositeComponent;
                $this->component->setParent($this->parent2);
            });

            test('$parent->hasChild($component) === false', function () {
                $this->assertFalse($this->parent->hasChild($this->component));
            });

            test('$parent2->hasChild($component) === true', function () {
                $this->assertTrue($this->parent2->hasChild($this->component));
            });

            test('$component->getParent() === $parent2', function () {
                $this->assertEquals($this->parent2, $this->component->getParent());
            });
        });
    });

    testCase('$component->setParent($parent = new \NubecuLabs\Components\Tests\CompositeComponent, false);', function () {
        setUp(function () {
            $this->parent = new CompositeComponent;
            $this->component->setParent($this->parent, false);
        });

        test('$component->getParent() === $parent', function () {
            $this->assertEquals($this->parent, $this->component->getParent());
        });

        test('$parent->hasChild($component) === false', function () {
            $this->assertFalse($this->parent->hasChild($this->component));
        });
    });

    testCase('$component->getEventDispatcher();', function () {
        test('returns an instance of "Symfony\Component\EventDispatcher\EventDispatcher"', function () {
            $this->assertInstanceOf(EventDispatcher::class, $this->component->getEventDispatcher());
        });

        test('returns always the same instance', function () {
            $dispatcher = $this->component->getEventDispatcher();

            $this->assertSame($dispatcher, $this->component->getEventDispatcher());
            $this->assertSame($dispatcher, $this->component->getEventDispatcher());
        });

        testCase('$component->setEventDispatcher($newDispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher);', function () {
            test('$component->getEventDispatcher() === $newDispatcher', function () {
                $newDispatcher = new EventDispatcher;

                $this->component->setEventDispatcher($newDispatcher);

                $this->assertSame($newDispatcher, $this->component->getEventDispatcher());
            });
        });
    });
});
