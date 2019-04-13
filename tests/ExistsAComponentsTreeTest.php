<?php

use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\Tests\Entity\Component;
use NubecuLabs\Components\Tests\Entity\ComponentWithEvents;
use NubecuLabs\Components\Tests\Entity\CompositeComponent;
use NubecuLabs\Components\Tests\Entity\CompositeComponentWithEvents;
use Symfony\Component\EventDispatcher\Event;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('ExistsAComponentsTree.php', function () {
    testCase('exists a tree of components (see sources)', function () {
        /**
         * C:    Component
         * CWE:  ComponentWithEvents
         * CC:   CompositeComponent
         * CCWE: CompositeComponentWithEvents
         *
         * $component (CCWE)
         *     |
         *     |____$child1 (C)
         *     |____$child2 (CWE)
         *     |____$child3 (CC)
         *     |       |
         *     |       |____$child31 (CC)
         *     |       |____$child32 (C)
         *     |       |____$child33 (CWE)
         *     |
         *     |____$child4 (CCWE)
         *     |       |
         *     |       |____$child41 (CCWE)
         *     |               |____$child411 (CC)
         *     |               |____$child412 (CWE)
         */
        setUpBeforeClassOnce(function () {
            $component = new CompositeComponentWithEvents('component');
            $child1 = new Component('child1');
            $child2 = new ComponentWithEvents('child2');
            $child3 = new CompositeComponent('child3');
            $child31 = new CompositeComponent('child31');
            $child32 = new Component('child32');
            $child33 = new ComponentWithEvents('child33');
            $child4 = new CompositeComponentWithEvents('child4');
            $child41 = new CompositeComponentWithEvents('child41');
            $child411 = new CompositeComponent('child411');
            $child412 = new ComponentWithEvents('child412');

            $component->addChilds($child1, $child2, $child3, $child4);
            $child3->addChilds($child31, $child32, $child33);
            $child4->addChilds($child41);
            $child41->addChilds($child411, $child412);

            static::addVars(compact(
                'component',
                'child1',
                'child2',
                'child3',
                'child31',
                'child32',
                'child33',
                'child4',
                'child41',
                'child411',
                'child412'
            ));
        });

        setUp(function () {
            $this->injectVars();
        });

        // $id = uniqid('comp');
        // test("\$component->findChildById('$id') === null", function () use ($id) {
        //     $this->assertNull($this->component->findChildById($id));
        // });

        // test('$component->findChildById("child4") === $child4', function () {
        //     $this->assertSame($this->child4, $this->component->findChildById('child4'));
        // });

        // testCase('$iterator = $component->children();', function () {
        //     setUpBeforeClassOnce(function () {
        //         $component = static::getVar('component');
        //         static::setVar('iterator', $component->children());
        //     });

        //     test('iteration #1: $iterator->current() === $child1', function () {
        //         $this->assertSame($this->child1, $this->iterator->current());
        //         $this->iterator->next();
        //     });

        //     test('iteration #2: $iterator->current() === $child2', function () {
        //         $this->assertSame($this->child2, $this->iterator->current());
        //         $this->iterator->next();
        //     });

        //     test('iteration #3: $iterator->current() === $child3', function () {
        //         $this->assertSame($this->child3, $this->iterator->current());
        //         $this->iterator->next();
        //     });

        //     test('iteration #4: $iterator->current() === $child4', function () {
        //         $this->assertSame($this->child4, $this->iterator->current());
        //         $this->iterator->next();
        //     });

        //     test('iteration #5: $iterator->current() === $child5', function () {
        //         $this->assertSame($this->child5, $this->iterator->current());
        //         $this->iterator->next();
        //     });

        //     test('iteration #6: $iterator->current() === $child6', function () {
        //         $this->assertSame($this->child6, $this->iterator->current());
        //         $this->iterator->next();
        //     });

        //     test('iteration #7: $iterator->current() === $child7', function () {
        //         $this->assertSame($this->child7, $this->iterator->current());
        //         $this->iterator->next();
        //     });

        //     test('iteration #8: $iterator->current() === $child8', function () {
        //         $this->assertSame($this->child8, $this->iterator->current());
        //         $this->iterator->next();
        //     });

        //     test('iteration #9: $iterator->current() === $child9', function () {
        //         $this->assertSame($this->child9, $this->iterator->current());
        //         $this->iterator->next();
        //     });

        //     test('iteration #10 (End): $iterator->current() === $child10', function () {
        //         $this->assertSame($this->child10, $this->iterator->current());
        //         $this->iterator->next();
        //         $this->assertNull($this->iterator->current());
        //     });
        // });

        // testCase('$iterator = $component->children(false);', function () {
        //     setUpBeforeClassOnce(function () {
        //         $component = static::getVar('component');
        //         static::setVar('iterator', $component->children(false));
        //     });

        //     test('iteration #1: $iterator->current() === $child1', function () {
        //         $this->assertSame($this->child1, $this->iterator->current());
        //         $this->iterator->next();
        //     });

        //     test('iteration #2: $iterator->current() === $child2', function () {
        //         $this->assertSame($this->child2, $this->iterator->current());
        //         $this->iterator->next();
        //     });

        //     test('iteration #3 (End): $iterator->current() === $child5', function () {
        //         $this->assertSame($this->child5, $this->iterator->current());
        //         $this->iterator->next();
        //         $this->assertNull($this->iterator->current());
        //     });
        // });

        // testCase('$component->findChild($callback) cases', function () {
        //     test('returns null when callback always returns false', function () {
        //         $this->assertNull($this->component->findChild(function (ComponentInterface $child) {
        //             return false;
        //         }));
        //     });

        //     test('returns null when the callback not returns nothing', function () {
        //         $this->assertNull($this->component->findChild(function (ComponentInterface $child) {
        //         }));
        //     });

        //     test('returns the child for whom the callback returns a value', function () {
        //         $this->assertSame($this->child4, $this->component->findChild(function (ComponentInterface $child) {
        //             return $child->getId() == 'child4' ? true : false;
        //         }));

        //         $this->assertSame($this->child4, $this->component->findChild(function (ComponentInterface $child) {
        //             if ($child->getId() == 'child4') {
        //                 return $child;
        //             }
        //         }));
        //     });

        //     test('$component->findChild($callback, false) does not do a recursive search', function () {
        //         $callback = function (ComponentInterface $child) {
        //             return $child->getId() == 'child4' ? true : false;
        //         };

        //         $this->assertNull($this->component->findChild($callback, false));
        //     });
        // });

        // testCase('$component->findChilds($callback) cases', function () {
        //     test('returns an empty array when callback always return false', function () {
        //         $callback = function () {
        //             return false;
        //         };

        //         $this->assertEquals([], $this->component->findChilds($callback));
        //     });

        //     test('returns an empty array when callback not returns nothing', function () {
        //         $callback = function () {
        //             return false;
        //         };

        //         $this->assertEquals([], $this->component->findChilds($callback));
        //     });

        //     test('returns in an array the childs for whom the callback returns true', function () {
        //         $callback = function (ComponentInterface $child) {
        //             return $child instanceof CompositeComponent ? false : true;
        //         };

        //         $childs = $this->component->findChilds($callback);

        //         $this->assertCount(2, $childs);
        //         $this->assertSame($this->child1, $childs[0]);
        //         $this->assertSame($this->child4, $childs[1]);
        //     });

        //     test('returns in an array the childs for whom the callback returns a value', function () {
        //         $callback = function (ComponentInterface $child) {
        //             if (! $child instanceof CompositeComponent) {
        //                 return $child;
        //             }
        //         };

        //         $childs = $this->component->findChilds($callback);

        //         $this->assertCount(2, $childs);
        //         $this->assertSame($this->child1, $childs[0]);
        //         $this->assertSame($this->child4, $childs[1]);
        //     });

        //     test('$component->findChilds($callback, false) does not do a recursive search', function () {
        //         $callback = function (ComponentInterface $child) {
        //             return $child instanceof CompositeComponent ? false : true;
        //         };

        //         $childs = $this->component->findChilds($callback, false);

        //         $this->assertCount(1, $childs);
        //         $this->assertSame($this->child1, $childs[0]);
        //     });
        // });

        // testCase('$parents = $child1->getParents();', function () {
        //     setUp(function () {
        //         $this->parents = $this->child1->getParents();
        //     });

        //     test('count($parents) == 1', function () {
        //         $this->assertCount(1, $this->parents);
        //     });

        //     test('$parents[0] === $component', function () {
        //         $this->assertSame($this->component, $this->parents[0]);
        //     });
        // });

        // testCase('$parents = $child8->getParents();', function () {
        //     setUp(function () {
        //         $this->parents = $this->child8->getParents();
        //     });

        //     test('count($parents) == 4', function () {
        //         $this->assertCount(4, $this->parents);
        //     });

        //     test('$parents[0] === $child7', function () {
        //         $this->assertSame($this->child7, $this->parents[0]);
        //     });

        //     test('$parents[1] === $child6', function () {
        //         $this->assertSame($this->child6, $this->parents[1]);
        //     });

        //     test('$parents[2] === $child5', function () {
        //         $this->assertSame($this->child5, $this->parents[2]);
        //     });

        //     test('$parents[3] === $component', function () {
        //         $this->assertSame($this->component, $this->parents[3]);
        //     });
        // });

        // testCase('exists a subtree listening for an event (see sources)', function () {
        //     setUp(function () {
        //         $this->eventName = uniqid('event');
        //         $this->event = new Event;
        //         $this->momentCapture1 = null;
        //         $this->momentCapture2 = null;
        //         $this->moment = null;
        //         $this->momentBubble1 = null;
        //         $this->momentBubble2 = null;

        //         $this->component->on($this->eventName, $this->listenerCapture1 = function () {
        //             $this->momentCapture1 = microtime(true);
        //         }, true);

        //         $this->child2->on($this->eventName, $this->listenerCapture2 = function () {
        //             $this->momentCapture2 = microtime(true);
        //         }, true);

        //         $this->child4->on($this->eventName, function () {
        //             $this->moment = microtime(true);
        //         });

        //         $this->child2->on($this->eventName, function () {
        //             $this->momentBubble1 = microtime(true);
        //         });

        //         $this->component->on($this->eventName, function () {
        //             $this->momentBubble2 = microtime(true);
        //         });
        //     });

        //     test('testing order of event propagation from capture to bubbling', function () {
        //         $this->child4->dispatch($this->eventName, $this->event);

        //         $this->assertGreaterThan($this->momentCapture1, $this->momentCapture2);
        //         $this->assertGreaterThan($this->momentCapture2, $this->moment);
        //         $this->assertGreaterThan($this->moment, $this->momentBubble1);
        //         $this->assertGreaterThan($this->momentBubble1, $this->momentBubble2);
        //     });

        //     test('testing order of event propagation when capture is disabled', function () {
        //         $this->child4->dispatch($this->eventName, $this->event, false);

        //         $this->assertNull($this->momentCapture1);
        //         $this->assertNull($this->momentCapture2);
        //         $this->assertGreaterThan($this->moment, $this->momentBubble1);
        //         $this->assertGreaterThan($this->momentBubble1, $this->momentBubble2);
        //     });

        //     test('testing order of event propagation when bubbling is disabled', function () {
        //         $this->child4->dispatch($this->eventName, $this->event, true, false);

        //         $this->assertGreaterThan($this->momentCapture1, $this->momentCapture2);
        //         $this->assertGreaterThan($this->momentCapture2, $this->moment);
        //         $this->assertNull($this->momentBubble1);
        //         $this->assertNull($this->momentBubble2);
        //     });

        //     test('testing order of event propagation when capture and bubbling are disabled', function () {
        //         $this->child4->dispatch($this->eventName, $this->event, false, false);

        //         $this->assertNull($this->momentCapture1);
        //         $this->assertNull($this->momentCapture2);
        //         $this->assertInternalType('float', $this->moment);
        //         $this->assertNull($this->momentBubble1);
        //         $this->assertNull($this->momentBubble2);
        //     });

        //     test('removing listeners of capture ', function () {
        //         $this->component->off($this->eventName, $this->listenerCapture1, true);
        //         $this->child2->off($this->eventName, $this->listenerCapture2, true);

        //         $this->child4->dispatch($this->eventName, $this->event);

        //         $this->assertNull($this->momentCapture1);
        //         $this->assertNull($this->momentCapture2);
        //         $this->assertGreaterThan($this->moment, $this->momentBubble1);
        //         $this->assertGreaterThan($this->momentBubble1, $this->momentBubble2);
        //     });
        // });
    });
});
