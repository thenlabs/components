<?php

use NubecuLabs\Components\Event\TreeEvent;
use NubecuLabs\Components\Event\AfterInsertionTreeEvent;
use NubecuLabs\Components\Event\BeforeInsertionTreeEvent;
use NubecuLabs\Components\Event\BeforeDeletionTreeEvent;
use NubecuLabs\Components\Tests\Component;
use NubecuLabs\Components\Tests\CompositeComponent;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('TreeEventsTest.php', function () {
    setUp(function () {
        $this->executedListenerBeforeInsertion1 = 0;
        $this->executedListenerBeforeInsertion2 = 0;
        $this->executedListenerBeforeDeletion1 = 0;
        $this->executedListenerBeforeDeletion2 = 0;
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

        $this->beforeDeletionListener1 = function (BeforeDeletionTreeEvent $event) {
            $this->executedListenerBeforeDeletion1++;
            $this->assertSame($this->parent, $event->getParent());
            $this->assertSame($this->child, $event->getChild());
            $this->assertSame($this->parent, $this->child->getParent());
            $this->assertTrue($this->parent->hasChild($this->child));
            $this->assertFalse($event->isCancelled());
        };

        $this->beforeDeletionListener2 = function (BeforeDeletionTreeEvent $event) {
            $this->executedListenerBeforeDeletion2++;
            $this->assertSame($this->parent, $event->getParent());
            $this->assertSame($this->child, $event->getChild());
            $this->assertSame($this->parent, $this->child->getParent());
            $this->assertTrue($this->parent->hasChild($this->child));

            $event->cancel();
            $this->assertTrue($event->isCancelled());
        };
    });

    testCase('testing the before insertion event', function () {
        testCase('the event is dispatched', function () {
            setUp(function () {
                $this->parent->on(TreeEvent::BEFORE_INSERTION, $this->beforeInsertionListener1);
            });

            createMethod('checkAsserts', function () {
                $this->assertEquals(1, $this->executedListenerBeforeInsertion1);
                $this->assertTrue($this->parent->hasChild($this->child));
                $this->assertSame($this->parent, $this->child->getParent());
            });

            test('when $parent->addChild($child);', function () {
                $this->parent->addChild($this->child); // Act

                $this->checkAsserts();
            });

            test('when $child->setParent($parent);', function () {
                $this->child->setParent($this->parent); // Act

                $this->checkAsserts();
            });
        });

        testCase('the event can cancel the insertion', function () {
            setUp(function () {
                $this->parent->on(TreeEvent::BEFORE_INSERTION, $this->beforeInsertionListener2);
            });

            createMethod('checkAsserts', function () {
                $this->assertEquals(1, $this->executedListenerBeforeInsertion2);
                $this->assertNull($this->child->getParent());
                $this->assertFalse($this->parent->hasChild($this->child));
            });

            test('when $parent->addChild($child);', function () {
                $this->parent->addChild($this->child); // Act

                $this->checkAsserts();
            });

            test('when $child->setParent($parent);', function () {
                $this->child->setParent($this->parent); // Act

                $this->checkAsserts();
            });
        });
    });

    testCase('testing the after insertion event', function () {
        testCase('the event is dispatched', function () {
            setUp(function () {
                $this->parent->on(TreeEvent::AFTER_INSERTION, $this->afterInsertionListener);
            });

            test('when $parent->addChild($child);', function () {
                $this->parent->addChild($this->child); // Act

                $this->assertEquals(1, $this->executedListenerAfterInsertion);
                $this->assertTrue($this->parent->hasChild($this->child));
                $this->assertSame($this->parent, $this->child->getParent());
            });

            test('when $child->setParent($parent);', function () {
                $this->child->setParent($this->parent); // Act

                $this->assertEquals(1, $this->executedListenerAfterInsertion);
                $this->assertTrue($this->parent->hasChild($this->child));
                $this->assertSame($this->parent, $this->child->getParent());
            });
        });
    });

    testCase('testing the before deletion event', function () {
        testCase('the event is dispatched', function () {
            setUp(function () {
                $this->parent->addChild($this->child);
                $this->parent->on(TreeEvent::BEFORE_DELETION, $this->beforeDeletionListener1);
            });

            createMethod('checkAsserts', function () {
                $this->assertEquals(1, $this->executedListenerBeforeDeletion1);
                $this->assertFalse($this->parent->hasChild($this->child));
                $this->assertNull($this->child->getParent());
            });

            test('when $parent->dropChild($child);', function () {
                $this->parent->dropChild($this->child); // Act

                $this->checkAsserts();
            });

            test('when $child->setParent(null);', function () {
                $this->child->setParent(null); // Act

                $this->checkAsserts();
            });
        });

        testCase('the event can cancel the deletion', function () {
            setUp(function () {
                $this->parent->addChild($this->child);
                $this->parent->on(TreeEvent::BEFORE_DELETION, $this->beforeDeletionListener2);
            });

            createMethod('checkAsserts', function () {
                $this->assertEquals(1, $this->executedListenerBeforeDeletion2);
                $this->assertTrue($this->parent->hasChild($this->child));
                $this->assertSame($this->parent, $this->child->getParent());
            });

            test('when $parent->dropChild($child);', function () {
                $this->parent->dropChild($this->child); // Act

                $this->checkAsserts();
            });

            test('when $child->setParent(null);', function () {
                $this->child->setParent(null); // Act

                $this->checkAsserts();
            });
        });
    });
});
