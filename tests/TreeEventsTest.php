<?php

use NubecuLabs\Components\Event\TreeEvent;
use NubecuLabs\Components\Event\AfterInsertionTreeEvent;
use NubecuLabs\Components\Event\AfterDeletionTreeEvent;
use NubecuLabs\Components\Event\BeforeInsertionTreeEvent;
use NubecuLabs\Components\Event\BeforeDeletionTreeEvent;
use NubecuLabs\Components\Tests\Entity\Component;
use NubecuLabs\Components\Tests\Entity\CompositeComponent;

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

    testCase('testing tree events propagations', function () {
        setUp(function () {
            $this->executedCaptureListener1 = null;
            $this->executedCaptureListener2 = null;
            $this->executedListener = null;
            $this->executedBubblesListener1 = null;
            $this->executedBubblesListener2 = null;

            $this->component1 = new CompositeComponent;
            $this->component2 = new CompositeComponent;
            $this->component3 = new CompositeComponent;

            $this->component1->addChild($this->component2);
            $this->component2->addChild($this->component3);

            $this->child = new Component;
        });

        testCase('testing insertion events', function () {
            createMacro('commons of insertion events', function () {
                createMacro('tests', function () {
                    test('listeners are executed in order', function () {
                        $this->assertGreaterThan($this->executedCaptureListener1, $this->executedCaptureListener2);
                        $this->assertGreaterThan($this->executedCaptureListener2, $this->executedListener);
                        $this->assertGreaterThan($this->executedListener, $this->executedBubblesListener2);
                        $this->assertGreaterThan($this->executedBubblesListener2, $this->executedBubblesListener1);
                    });
                });

                testCase('$parent->addChild($child);', function () {
                    setUp(function () {
                        $this->component3->addChild($this->child);
                    });

                    useMacro('tests');
                });

                testCase('$child->setParent($parent);', function () {
                    setUp(function () {
                        $this->child->setParent($this->component3);
                    });

                    useMacro('tests');
                });
            });

            testCase('testing propagation of BeforeInsertionTreeEvent', function () {
                setUp(function () {
                    $this->checkEventData = function ($event) {
                        $this->assertSame($this->component3, $event->getParent());
                        $this->assertSame($this->child, $event->getChild());

                        $this->assertNull($event->getChild()->getParent());
                        $this->assertFalse($event->getParent()->hasChild($this->child->getId()));
                    };

                    $this->component1->on(TreeEvent::BEFORE_INSERTION, function (BeforeInsertionTreeEvent $event) {
                        call_user_func($this->checkEventData, $event);
                        $this->executedCaptureListener1 = new DateTime;
                    }, true); // Capture

                    $this->component2->on(TreeEvent::BEFORE_INSERTION, function (BeforeInsertionTreeEvent $event) {
                        call_user_func($this->checkEventData, $event);
                        $this->executedCaptureListener2 = new DateTime;
                    }, true); // Capture

                    $this->component3->on(TreeEvent::BEFORE_INSERTION, function (BeforeInsertionTreeEvent $event) {
                        call_user_func($this->checkEventData, $event);
                        $this->executedListener = new DateTime;
                    });

                    $this->component2->on(TreeEvent::BEFORE_INSERTION, function (BeforeInsertionTreeEvent $event) {
                        call_user_func($this->checkEventData, $event);
                        $this->executedBubblesListener2 = new DateTime;
                    }); // Bubbling

                    $this->component1->on(TreeEvent::BEFORE_INSERTION, function (BeforeInsertionTreeEvent $event) {
                        call_user_func($this->checkEventData, $event);
                        $this->executedBubblesListener1 = new DateTime;
                    }); // Bubbling
                });

                useMacro('commons of insertion events');
            });

            testCase('testing propagation of AfterInsertionTreeEvent', function () {
                setUp(function () {
                    $this->checkEventData = function ($event) {
                        $this->assertSame($this->component3, $event->getParent());
                        $this->assertSame($this->child, $event->getChild());

                        $this->assertSame($this->component3, $event->getChild()->getParent());
                        $this->assertTrue($event->getParent()->hasChild($this->child->getId()));
                    };

                    $this->component1->on(TreeEvent::AFTER_INSERTION, function (AfterInsertionTreeEvent $event) {
                        call_user_func($this->checkEventData, $event);
                        $this->executedCaptureListener1 = new DateTime;
                    }, true); // Capture

                    $this->component2->on(TreeEvent::AFTER_INSERTION, function (AfterInsertionTreeEvent $event) {
                        call_user_func($this->checkEventData, $event);
                        $this->executedCaptureListener2 = new DateTime;
                    }, true); // Capture

                    $this->component3->on(TreeEvent::AFTER_INSERTION, function (AfterInsertionTreeEvent $event) {
                        call_user_func($this->checkEventData, $event);
                        $this->executedListener = new DateTime;
                    });

                    $this->component2->on(TreeEvent::AFTER_INSERTION, function (AfterInsertionTreeEvent $event) {
                        call_user_func($this->checkEventData, $event);
                        $this->executedBubblesListener2 = new DateTime;
                    }); // Bubbling

                    $this->component1->on(TreeEvent::AFTER_INSERTION, function (AfterInsertionTreeEvent $event) {
                        call_user_func($this->checkEventData, $event);
                        $this->executedBubblesListener1 = new DateTime;
                    }); // Bubbling
                });

                useMacro('commons of insertion events');
            });
        });

        testCase('testing deletion events', function () {
            createMacro('commons of deletion events', function () {
                createMacro('tests', function () {
                    test('listeners are executed in order', function () {
                        $this->assertGreaterThan($this->executedCaptureListener1, $this->executedCaptureListener2);
                        $this->assertGreaterThan($this->executedCaptureListener2, $this->executedListener);
                        $this->assertGreaterThan($this->executedListener, $this->executedBubblesListener2);
                        $this->assertGreaterThan($this->executedBubblesListener2, $this->executedBubblesListener1);
                    });
                });

                testCase('$parent->dropChild($child);', function () {
                    setUp(function () {
                        $this->component3->dropChild($this->child);
                    });

                    useMacro('tests');
                });

                testCase('$parent->dropChild($child->getId());', function () {
                    setUp(function () {
                        $this->component3->dropChild($this->child->getId());
                    });

                    useMacro('tests');
                });

                testCase('$child->setParent(null);', function () {
                    setUp(function () {
                        $this->child->setParent(null);
                    });

                    useMacro('tests');
                });
            });

            testCase('testing propagation of BeforeDeletionTreeEvent', function () {
                setUp(function () {
                    $this->component3->addChild($this->child);

                    $this->checkEventData = function ($event) {
                        $this->assertSame($this->component3, $event->getParent());
                        $this->assertSame($this->child, $event->getChild());

                        $this->assertSame($this->component3, $event->getChild()->getParent());
                        $this->assertTrue($event->getParent()->hasChild($this->child->getId()));
                    };

                    $this->component1->on(TreeEvent::BEFORE_DELETION, function (BeforeDeletionTreeEvent $event) {
                        call_user_func($this->checkEventData, $event);
                        $this->executedCaptureListener1 = new DateTime;
                    }, true); // Capture

                    $this->component2->on(TreeEvent::BEFORE_DELETION, function (BeforeDeletionTreeEvent $event) {
                        call_user_func($this->checkEventData, $event);
                        $this->executedCaptureListener2 = new DateTime;
                    }, true); // Capture

                    $this->component3->on(TreeEvent::BEFORE_DELETION, function (BeforeDeletionTreeEvent $event) {
                        call_user_func($this->checkEventData, $event);
                        $this->executedListener = new DateTime;
                    });

                    $this->component2->on(TreeEvent::BEFORE_DELETION, function (BeforeDeletionTreeEvent $event) {
                        call_user_func($this->checkEventData, $event);
                        $this->executedBubblesListener2 = new DateTime;
                    }); // Bubbling

                    $this->component1->on(TreeEvent::BEFORE_DELETION, function (BeforeDeletionTreeEvent $event) {
                        call_user_func($this->checkEventData, $event);
                        $this->executedBubblesListener1 = new DateTime;
                    }); // Bubbling
                });

                useMacro('commons of deletion events');
            });

            // testCase('testing propagation of AfterInsertionTreeEvent', function () {
            //     setUp(function () {
            //         $this->checkEventData = function ($event) {
            //             $this->assertSame($this->component3, $event->getParent());
            //             $this->assertSame($this->child, $event->getChild());

            //             $this->assertSame($this->component3, $event->getChild()->getParent());
            //             $this->assertTrue($event->getParent()->hasChild($this->child->getId()));
            //         };

            //         $this->component1->on(TreeEvent::AFTER_INSERTION, function (AfterInsertionTreeEvent $event) {
            //             call_user_func($this->checkEventData, $event);
            //             $this->executedCaptureListener1 = new DateTime;
            //         }, true); // Capture

            //         $this->component2->on(TreeEvent::AFTER_INSERTION, function (AfterInsertionTreeEvent $event) {
            //             call_user_func($this->checkEventData, $event);
            //             $this->executedCaptureListener2 = new DateTime;
            //         }, true); // Capture

            //         $this->component3->on(TreeEvent::AFTER_INSERTION, function (AfterInsertionTreeEvent $event) {
            //             call_user_func($this->checkEventData, $event);
            //             $this->executedListener = new DateTime;
            //         });

            //         $this->component2->on(TreeEvent::AFTER_INSERTION, function (AfterInsertionTreeEvent $event) {
            //             call_user_func($this->checkEventData, $event);
            //             $this->executedBubblesListener2 = new DateTime;
            //         }); // Bubbling

            //         $this->component1->on(TreeEvent::AFTER_INSERTION, function (AfterInsertionTreeEvent $event) {
            //             call_user_func($this->checkEventData, $event);
            //             $this->executedBubblesListener1 = new DateTime;
            //         }); // Bubbling
            //     });

            //     useMacro('commons of insertion events');
            // });
        });
    });
});
