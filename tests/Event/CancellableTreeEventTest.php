<?php

use NubecuLabs\Components\Tests\Component;
use NubecuLabs\Components\Tests\CompositeComponent;
use NubecuLabs\Components\Event\CancellableTreeEvent;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('CancellableTreeEventTest.php', function () {
    testCase('$event = new Event\CancellableTreeEvent($child = new Component, $parent = new CompositeComponent);', function () {
        setUp(function () {
            $this->child = new Component;
            $this->parent = new CompositeComponent;
            $this->event = new CancellableTreeEvent($this->child, $this->parent);
        });

        test('$event->getChild() === $child', function () {
            $this->assertSame($this->child, $this->event->getChild());
        });

        test('$event->getParent() === $parent', function () {
            $this->assertSame($this->parent, $this->event->getParent());
        });

        test('$event->isCancelled() === false', function () {
            $this->assertFalse($this->event->isCancelled());
        });

        testCase('$event->cancel();', function () {
            setUp(function () {
                $this->event->cancel();
            });

            test('$event->isCancelled() === true', function () {
                $this->assertTrue($this->event->isCancelled());
            });

            testCase('$event->cancel(false);', function () {
                setUp(function () {
                    $this->event->cancel(false);
                });

                test('$event->isCancelled() === false', function () {
                    $this->assertFalse($this->event->isCancelled());
                });
            });
        });
    });
});
