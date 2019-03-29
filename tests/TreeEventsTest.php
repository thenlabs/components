<?php

use NubecuLabs\Components\Event\TreeEvent;
use NubecuLabs\Components\Event\AfterInsertionTreeEvent;
use NubecuLabs\Components\Event\BeforeInsertionTreeEvent;
use NubecuLabs\Components\Tests\Component;
use NubecuLabs\Components\Tests\CompositeComponent;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('TreeEventsTest.php', function () {
    setUp(function () {
        $this->executedListenerBeforeInsertion1 = 0;
        $this->executedListenerBeforeInsertion2 = 0;
        $this->executedListenerAfterInsertion = 0;
        $this->child = new Component;
        $this->parent = new CompositeComponent;

        $this->beforeInsertionListener1 = function (BeforeInsertionTreeEvent $event) {
            $this->executedListenerBeforeInsertion1++;
            $this->assertSame($this->parent, $event->getParent());
            $this->assertSame($this->child, $event->getChild());
            $this->assertNull($this->child->getParent());
            $this->assertFalse($this->parent->hasChild($this->child));
            $this->assertFalse($event->isCancelled());
        };

        $this->beforeInsertionListener2 = function (BeforeInsertionTreeEvent $event) {
            $this->executedListenerBeforeInsertion2++;

            $this->assertSame($this->parent, $event->getParent());
            $this->assertSame($this->child, $event->getChild());
            $this->assertNull($this->child->getParent());
            $this->assertFalse($this->parent->hasChild($this->child));
            $this->assertFalse($event->isCancelled());

            $event->cancel();
            $this->assertTrue($event->isCancelled());
        };

        $this->afterInsertionListener = function (AfterInsertionTreeEvent $event) {
            $this->executedListenerAfterInsertion = true;
            $this->assertSame($this->parent, $event->getParent());
            $this->assertSame($this->child, $event->getChild());
        };
    });

    testCase('testing the before insertion event', function () {
        testCase('the event is dispatched', function () {
            setUp(function () {
                $this->parent->on(TreeEvent::BEFORE_INSERTION, $this->beforeInsertionListener1);
            });

            test('when $parent->addChild($child);', function () {
                $this->parent->addChild($this->child); // Act

                $this->assertEquals(1, $this->executedListenerBeforeInsertion1);
                $this->assertTrue($this->parent->hasChild($this->child));
            });

            test('when $child->setParent($parent);', function () {
                $this->child->setParent($this->parent); // Act

                $this->assertEquals(1, $this->executedListenerBeforeInsertion1);
                $this->assertTrue($this->parent->hasChild($this->child));
            });
        });

        testCase('the event can cancel the insertion', function () {
            setUp(function () {
                $this->parent->on(TreeEvent::BEFORE_INSERTION, $this->beforeInsertionListener2);
            });

            test('when $parent->addChild($child);', function () {
                $this->parent->addChild($this->child); // Act

                $this->assertEquals(1, $this->executedListenerBeforeInsertion2);
                $this->assertFalse($this->parent->hasChild($this->child));
            });

            test('when $child->setParent($parent);', function () {
                $this->child->setParent($this->parent); // Act

                $this->assertEquals(1, $this->executedListenerBeforeInsertion2);
                $this->assertFalse($this->parent->hasChild($this->child));
            });
        });
    });

    // testCase('testing the after insertion event', function () {
    //     testCase('the event is dispatched', function () {
    //         setUp(function () {
    //             $this->parent->on(TreeEvent::AFTER_INSERTION, $this->afterInsertionListener);
    //         });

    //         test('when $parent->addChild($child);', function () {
    //             $this->parent->addChild($this->child); // Act

    //             $this->assertTrue($this->executedListenerAfterInsertion);
    //             $this->assertTrue($this->parent->hasChild($this->child));
    //         });

    //         test('when $child->setParent($parent);', function () {
    //             $this->child->setParent($this->parent); // Act

    //             $this->assertTrue($this->executedListenerAfterInsertion);
    //             $this->assertTrue($this->parent->hasChild($this->child));
    //         });
    //     });
    // });
});
