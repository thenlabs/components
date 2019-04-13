<?php

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;

createMacro('commons of ComponentWithEvents and CompositeComponentWithEvents', function () {
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
