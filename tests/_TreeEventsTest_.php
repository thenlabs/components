<?php

use NubecuLabs\Components\Event\TreeEvent;
use NubecuLabs\Components\Event\AfterInsertionTreeEvent;
use NubecuLabs\Components\Event\AfterDeletionTreeEvent;
use NubecuLabs\Components\Event\BeforeInsertionTreeEvent;
use NubecuLabs\Components\Event\BeforeDeletionTreeEvent;
use NubecuLabs\Components\Tests\Component;
use NubecuLabs\Components\Tests\CompositeComponent;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('TreeEventsTest.php', function () {
    createMacro('testing events in the parent and child relationship', function () {
        testCase(function () {
            setUp(function () {
                $this->executedListenerBeforeInsertion1 = 0;
                $this->executedListenerBeforeInsertion2 = 0;
                $this->executedListenerBeforeDeletion1 = 0;
                $this->executedListenerBeforeDeletion2 = 0;
                $this->executedListenerAfterInsertion = 0;
                $this->executedListenerAfterDeletion = 0;
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
                    $this->executedListenerAfterInsertion++;
                    $this->assertSame($this->parent, $event->getParent());
                    $this->assertSame($this->child, $event->getChild());
                    $this->assertSame($this->parent, $this->child->getParent());
                    $this->assertTrue($this->parent->hasChild($this->child));
                    $this->assertTrue($this->parent->hasChild($this->child->getId()));
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

                $this->afterDeletionListener = function (AfterDeletionTreeEvent $event) {
                    $this->executedListenerAfterDeletion++;
                    $this->assertSame($this->parent, $event->getParent());
                    $this->assertSame($this->child, $event->getChild());
                    $this->assertNull($this->child->getParent());
                    $this->assertFalse($this->parent->hasChild($this->child));
                    $this->assertFalse($this->parent->hasChild($this->child->getId()));
                };
            });

            testCase('testing the before insertion event', function () {
                testCase('the event is dispatched', function () {
                    setUp(function () {
                        $this->parent->on(TreeEvent::BEFORE_INSERTION, $this->beforeInsertionListener1);
                    });

                    tearDown(function () {
                        $this->assertEquals(1, $this->executedListenerBeforeInsertion1);
                        $this->assertTrue($this->parent->hasChild($this->child));
                        $this->assertSame($this->parent, $this->child->getParent());
                    });

                    test('when $parent->addChild($child);', function () {
                        $this->parent->addChild($this->child);
                    });

                    test('when $child->setParent($parent);', function () {
                        $this->child->setParent($this->parent);
                    });
                });

                testCase('the event can cancel the insertion', function () {
                    setUp(function () {
                        $this->parent->on(TreeEvent::BEFORE_INSERTION, $this->beforeInsertionListener2);
                    });

                    tearDown(function () {
                        $this->assertEquals(1, $this->executedListenerBeforeInsertion2);
                        $this->assertNull($this->child->getParent());
                        $this->assertFalse($this->parent->hasChild($this->child));
                    });

                    test('when $parent->addChild($child);', function () {
                        $this->parent->addChild($this->child);
                    });

                    test('when $child->setParent($parent);', function () {
                        $this->child->setParent($this->parent);
                    });
                });
            });

            testCase('testing the after insertion event', function () {
                testCase('the event is dispatched', function () {
                    setUp(function () {
                        $this->parent->on(TreeEvent::AFTER_INSERTION, $this->afterInsertionListener);
                    });

                    tearDown(function () {
                        $this->assertEquals(1, $this->executedListenerAfterInsertion);
                        $this->assertTrue($this->parent->hasChild($this->child));
                        $this->assertSame($this->parent, $this->child->getParent());
                    });

                    test('when $parent->addChild($child);', function () {
                        $this->parent->addChild($this->child);
                    });

                    test('when $child->setParent($parent);', function () {
                        $this->child->setParent($this->parent);
                    });
                });
            });

            createMacro('deletion tests', function () {
                test('when $parent->dropChild($child);', function () {
                    $this->parent->dropChild($this->child);
                });

                test('when $child->setParent(null);', function () {
                    $this->child->setParent(null);
                });

                test('when $child->setParent($parent2 = new CompositeComponent);', function () {
                    $this->newParent = new CompositeComponent;
                    $this->child->setParent($this->newParent);
                });
            });

            testCase('testing the before deletion event', function () {
                testCase('the event is dispatched', function () {
                    setUp(function () {
                        $this->parent->addChild($this->child);
                        $this->parent->on(TreeEvent::BEFORE_DELETION, $this->beforeDeletionListener1);
                    });

                    tearDown(function () {
                        $this->assertEquals(1, $this->executedListenerBeforeDeletion1);
                        $this->assertFalse($this->parent->hasChild($this->child));
                    });

                    useMacro('deletion tests');
                });

                testCase('the event can cancel the deletion', function () {
                    setUp(function () {
                        $this->parent->addChild($this->child);
                        $this->parent->on(TreeEvent::BEFORE_DELETION, $this->beforeDeletionListener2);
                    });

                    tearDown(function () {
                        $this->assertEquals(1, $this->executedListenerBeforeDeletion2);
                        $this->assertTrue($this->parent->hasChild($this->child));
                        $this->assertSame($this->parent, $this->child->getParent());
                    });

                    useMacro('deletion tests');
                });
            });

            testCase('testing the after deletion event', function () {
                testCase('the event is dispatched', function () {
                    setUp(function () {
                        $this->parent->addChild($this->child);
                        $this->parent->on(TreeEvent::AFTER_DELETION, $this->afterDeletionListener);
                    });

                    tearDown(function () {
                        $this->assertEquals(1, $this->executedListenerAfterDeletion);
                        $this->assertFalse($this->parent->hasChild($this->child));
                        $this->assertFalse($this->parent->hasChild($this->child->getId()));
                    });

                    useMacro('deletion tests');
                });
            });
        });
    });

    testCase('when the child is an instance of ComponentInterface', function () {
        setUp(function () {
            $this->child = new Component;
        });

        useMacro('testing events in the parent and child relationship');
    });

    testCase('when the child is an instance of CompositeComponentInterface', function () {
        setUp(function () {
            $this->child = new CompositeComponent;
        });

        useMacro('testing events in the parent and child relationship');
    });
});
