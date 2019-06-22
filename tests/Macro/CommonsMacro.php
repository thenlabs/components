<?php

use NubecuLabs\Components\Tests\Entity\CompositeComponent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;

createMacro('commons', function () {
    setUp(function () {
        $this->component = $this->getNewComponent();
    });

    test('$component->getId() != null', function () {
        $id = $this->component->getId();

        $this->assertGreaterThan(13, strlen($id));
        $this->assertStringStartsWith('comp_', $id);
    });

    test('$component->getName() === null', function () {
        $this->assertNull($this->component->getName());
    });

    test('$component->getParent() === null', function () {
        $this->assertNull($this->component->getParent());
    });

    test('$component->getOwnDependencies() === []', function () {
        $this->assertSame([], $this->component->getOwnDependencies());
    });

    test('$component->getAdditionalDependencies() === []', function () {
        $this->assertSame([], $this->component->getAdditionalDependencies());
    });

    test('$component->getDependencies() === []', function () {
        $this->assertSame([], $this->component->getDependencies());
    });

    test('#getDependencies() returns result of merge the method #getOwnDependencies() and #getAdditionalDependencies() in Helper::sortDependencies()', function () {
        $this->markTestIncomplete();
    });

    test('$component->getAllData() === []', function () {
        $this->assertEquals([], $this->component->getAllData());
    });

    $key = uniqid('data');
    test("\$component->hasData('{$key}') === false", function () use ($key) {
        $this->assertFalse($this->component->hasData($key));
    });

    $value = mt_rand(1, 100);
    testCase("\$component->setData('{$key}', {$value});", function () use ($key, $value) {
        setUp(function () use ($key, $value) {
            $this->component->setData($key, $value);
        });

        test("\$component->getData('{$key}') == {$value}", function () use ($key, $value) {
            $this->assertEquals($value, $this->component->getData($key));
        });

        test("\$component->getAllData() == ['{$key}' => {$value}]", function () use ($key, $value) {
            $this->assertEquals(
                [$key => $value],
                $this->component->getAllData()
            );
        });

        test("\$component->hasData('{$key}') === true", function () use ($key) {
            $this->assertTrue($this->component->hasData($key));
        });
    });

    testCase('$component->getId();', function () {
        test('always returns the same value', function () {
            $id = $this->component->getId();

            $this->assertEquals($id, $this->component->getId());
            $this->assertEquals($id, $this->component->getId());
            $this->assertEquals($id, $this->component->getId());
        });
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

    testCase('$component->setParent(null);', function () {
        test('$component->getParent() === null', function () {
            $this->component->setParent(null);

            $this->assertNull($this->component->getParent());
        });
    });

    createMacro('tests for when the parent is assigned', function () {
        test('$component->getParent() === $parent', function () {
            $this->assertEquals($this->parent, $this->component->getParent());
        });

        test('$parent->hasChild($component) === true', function () {
            $this->assertTrue($this->parent->hasChild($this->component));
        });

        createMacro('remove the parent tests', function () {
            test('$component->getParent() === null', function () {
                $this->assertNull($this->component->getParent());
            });

            test('$parent->hasChild($component) === false', function () {
                $this->assertFalse($this->parent->hasChild($this->component));
            });
        });

        testCase('$component->setParent(null);', function () {
            setUp(function () {
                $this->component->setParent(null);
            });

            useMacro('remove the parent tests');
        });

        createMacro('tests for when a new parent is assigned', function () {
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

        testCase('$component->setParent($parent2 = new \NubecuLabs\Components\Tests\Entity\CompositeComponent);', function () {
            setUp(function () {
                $this->parent2 = new CompositeComponent;
                $this->component->setParent($this->parent2);
            });

            useMacro('tests for when a new parent is assigned');
        });
    });

    testCase('$component->setParent($parent = new \NubecuLabs\Components\Tests\Entity\CompositeComponent);', function () {
        setUp(function () {
            $this->parent = new CompositeComponent;
            $this->component->setParent($this->parent);
        });

        useMacro('tests for when the parent is assigned');
    });

    createMacro('tests for when the parent is assigned without add the child in the parent', function () {
        test('$component->getParent() === $parent', function () {
            $this->assertEquals($this->parent, $this->component->getParent());
        });

        test('$parent->hasChild($component) === false', function () {
            $this->assertFalse($this->parent->hasChild($this->component));
        });
    });

    testCase('$component->setParent($parent = new \NubecuLabs\Components\Tests\Entity\CompositeComponent, false);', function () {
        setUp(function () {
            $this->parent = new CompositeComponent;
            $this->component->setParent($this->parent, false);
        });

        useMacro('tests for when the parent is assigned without add the child in the parent');
    });

    testCase('$iterator = $component->parents();', function () {
        test('the iterator is empty', function () {
            $iterator = $this->component->parents();

            $this->assertNull($iterator->current());
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

    testCase('$component->on($eventName, $listener);', function () {
        setUp(function () {
            $this->executedListener = false;
            $this->eventName = uniqid('event');
            $this->event = new Event;

            $this->component->on($this->eventName, $this->listener = function (Event $event) {
                $this->executedListener = true;
                $this->assertSame($event, $this->event);
            });
        });

        testCase('$component->dispatch($eventName = "eventName", $event = new Event($component));', function () {
            setUp(function () {
                $this->component->dispatchEvent($this->eventName, $this->event);
            });

            test('$listener was executed with the event object as argument', function () {
                $this->assertTrue($this->executedListener);
            });
        });

        testCase('$component->off($eventName, $listener);', function () {
            setUp(function () {
                $this->component->off($this->eventName, $this->listener);
                $this->component->dispatchEvent($this->eventName, $this->event);
            });

            test('$listener was not executed', function () {
                $this->assertFalse($this->executedListener);
            });
        });
    });
});
