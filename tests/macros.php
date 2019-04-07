<?php

use Symfony\Component\EventDispatcher\Event;
use NubecuLabs\Components\Tests\Entity\CompositeComponent;
use Symfony\Component\EventDispatcher\EventDispatcher;

createMacro('common tests', function () {
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

    testCase('$component->setParent($parent = new \NubecuLabs\Components\Tests\Entity\CompositeComponent);', function () {
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

        testCase('$component->setParent($parent2 = new \NubecuLabs\Components\Tests\Entity\CompositeComponent);', function () {
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

    testCase('$component->setParent($parent = new \NubecuLabs\Components\Tests\Entity\CompositeComponent, false);', function () {
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
});

createMacro('common event tests', function () {
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
                $this->component->dispatch($this->eventName, $this->event);
            });

            test('$listener was executed with the event object as argument', function () {
                $this->assertTrue($this->executedListener);
            });
        });

        testCase('$component->off($eventName, $listener);', function () {
            setUp(function () {
                $this->component->off($this->eventName, $this->listener);
                $this->component->dispatch($this->eventName, $this->event);
            });

            test('$listener was not executed', function () {
                $this->assertFalse($this->executedListener);
            });
        });
    });
});
