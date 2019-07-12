<?php

use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\Tests\Entity\Component;
use NubecuLabs\Components\Tests\Entity\CompositeComponent;
use NubecuLabs\Components\Tests\Entity\CompositeComponentWithEvents;
use NubecuLabs\Components\Event\Event;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('ExistsAComponentsTree.php', function () {
    testCase('exists a tree of components (see sources)', function () {
        /**
         * C:    Component
         * CC:   CompositeComponent
         *
         * $component (CC)
         *     |
         *     |____$child1 (C)
         *     |____$child2 (C)
         *     |____$child3 (CC)
         *     |       |
         *     |       |____$child31 (CC)
         *     |       |____$child32 (C)
         *     |       |____$child33 (C)
         *     |
         *     |____$child4 (CC)
         *     |       |
         *     |       |____$child41 (CC)
         *     |               |____$child411 (CC)
         *     |               |____$child412 (C)
         *     |               |____$child413 (C)
         */
        setUpBeforeClassOnce(function () {
            $component = new CompositeComponent('component');
            $child1 = new Component('child1');
            $child2 = new Component('child2');
            $child3 = new CompositeComponent('child3');
            $child31 = new CompositeComponent('child31');
            $child32 = new Component('child32');
            $child33 = new Component('child33');
            $child4 = new CompositeComponent('child4');
            $child41 = new CompositeComponent('child41');
            $child411 = new CompositeComponent('child411');
            $child412 = new Component('child412');
            $child413 = new Component('child3');

            $component->addChilds($child1, $child2, $child3, $child4);
            $child3->addChilds($child31, $child32, $child33);
            $child4->addChilds($child41);
            $child41->addChilds($child411, $child412, $child413);

            $key = uniqid('data');
            $value1 = uniqid('val');
            $value2 = uniqid('val');

            $component->setData($key, $value1);
            $child3->setData($key, $value2);

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
                'child412',
                'child413',
                'key',
                'value1',
                'value2'
            ));
        });

        setUp(function () {
            $this->injectVars();
        });

        $id = uniqid('comp');
        test("\$component->findChildById('$id') === null", function () use ($id) {
            $this->assertNull($this->component->findChildById($id));
        });

        test('$component->findChildById("child412") === $child412', function () {
            $this->assertSame($this->child412, $this->component->findChildById('child412'));
        });

        test('$component->findChildByName("child412") === $child412', function () {
            $this->assertSame($this->child412, $this->component->findChildByName('child412'));
        });

        test('$component->findChildsByName("child3") === [$child3, $child413]', function () {
            $childs = $this->component->findChildsByName('child3');

            $this->assertCount(2, $childs);
            $this->assertEquals($this->child3, $childs[0]);
            $this->assertEquals($this->child413, $childs[1]);
        });

        test('$component->getOrder() === ["child1", "child2", "child3", "child4"]', function () {
            $this->assertEquals(
                ['child1', 'child2', 'child3', 'child4'],
                $this->component->getOrder()
            );
        });

        testCase('getTopData() looks up the nearest data', function () {
            test(function () {
                $this->assertEquals($this->value2, $this->child32->getTopData($this->key));
            });

            test(function () {
                $this->assertEquals($this->value1, $this->child413->getTopData($this->key));
            });

            test(function () {
                $this->assertNull($this->child413->getTopData(uniqid('data')));
            });

            test(function () {
                $this->child41->setData($this->key, null);

                $this->assertNull($this->child413->getTopData($this->key));
            });

            test(function () {
                $this->assertEquals($this->value2, $this->child3->getTopData($this->key));
            });

            test(function () {
                $this->assertEquals($this->value1, $this->child3->getTopData($this->key, false));
            });
        });

        testCase('$iterator = $component->children();', function () {
            setUpBeforeClassOnce(function () {
                $component = static::getVar('component');
                static::setVar('iterator', $component->children());
            });

            test('iteration #1: $iterator->current() === $child1', function () {
                $this->assertSame($this->child1, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #2: $iterator->current() === $child2', function () {
                $this->assertSame($this->child2, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #3: $iterator->current() === $child3', function () {
                $this->assertSame($this->child3, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #4: $iterator->current() === $child31', function () {
                $this->assertSame($this->child31, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #5: $iterator->current() === $child32', function () {
                $this->assertSame($this->child32, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #6: $iterator->current() === $child33', function () {
                $this->assertSame($this->child33, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #7: $iterator->current() === $child4', function () {
                $this->assertSame($this->child4, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #8: $iterator->current() === $child41', function () {
                $this->assertSame($this->child41, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #9: $iterator->current() === $child411', function () {
                $this->assertSame($this->child411, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #10: $iterator->current() === $child412', function () {
                $this->assertSame($this->child412, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #11 (End): $iterator->current() === $child413', function () {
                $this->assertSame($this->child413, $this->iterator->current());
                $this->iterator->next();
                $this->assertNull($this->iterator->current());
            });
        });

        testCase('$iterator = $component->children(false);', function () {
            setUpBeforeClassOnce(function () {
                $component = static::getVar('component');
                static::setVar('iterator', $component->children(false));
            });

            test('iteration #1: $iterator->current() === $child1', function () {
                $this->assertSame($this->child1, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #2: $iterator->current() === $child2', function () {
                $this->assertSame($this->child2, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #3 (End): $iterator->current() === $child3', function () {
                $this->assertSame($this->child3, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #3 (End): $iterator->current() === $child4', function () {
                $this->assertSame($this->child4, $this->iterator->current());
                $this->iterator->next();
                $this->assertNull($this->iterator->current());
            });
        });

        testCase('$component->findChild($callback) cases', function () {
            test('returns null when callback always returns false', function () {
                $this->assertNull($this->component->findChild(function (ComponentInterface $child) {
                    return false;
                }));
            });

            test('returns null when the callback not returns nothing', function () {
                $this->assertNull($this->component->findChild(function (ComponentInterface $child) {
                }));
            });

            test('returns the child for whom the callback returns a value', function () {
                $this->assertSame($this->child411, $this->component->findChild(function (ComponentInterface $child) {
                    return $child->getId() == 'child411' ? true : false;
                }));

                $this->assertSame($this->child411, $this->component->findChild(function (ComponentInterface $child) {
                    if ($child->getId() == 'child411') {
                        return $child;
                    }
                }));
            });

            test('$component->findChild($callback, false) does not do a recursive search', function () {
                $callback = function (ComponentInterface $child) {
                    return $child->getId() == 'child411' ? true : false;
                };

                $this->assertNull($this->component->findChild($callback, false));
            });
        });

        testCase('$component->findChilds($callback) cases', function () {
            test('returns an empty array when callback always return false', function () {
                $callback = function () {
                    return false;
                };

                $this->assertEquals([], $this->component->findChilds($callback));
            });

            test('returns an empty array when callback not returns nothing', function () {
                $callback = function () {
                    return false;
                };

                $this->assertEquals([], $this->component->findChilds($callback));
            });

            test('returns in an array the childs for whom the callback returns true', function () {
                $callback = function (ComponentInterface $child) {
                    if ($child instanceof CompositeComponent
                    ) {
                        return true;
                    } else {
                        return false;
                    }
                };

                $childs = $this->component->findChilds($callback);

                $this->assertCount(5, $childs);
                $this->assertSame($this->child3, $childs[0]);
                $this->assertSame($this->child31, $childs[1]);
                $this->assertSame($this->child4, $childs[2]);
                $this->assertSame($this->child41, $childs[3]);
                $this->assertSame($this->child411, $childs[4]);
            });

            test('returns in an array the childs for whom the callback returns a value', function () {
                $callback = function (ComponentInterface $child) {
                    if ($child instanceof CompositeComponent) {
                        return $child;
                    }
                };

                $childs = $this->component->findChilds($callback);

                $this->assertCount(5, $childs);
                $this->assertSame($this->child3, $childs[0]);
                $this->assertSame($this->child31, $childs[1]);
                $this->assertSame($this->child4, $childs[2]);
                $this->assertSame($this->child41, $childs[3]);
                $this->assertSame($this->child411, $childs[4]);
            });

            test('$component->findChilds($callback, false) does not do a recursive search', function () {
                $callback = function (ComponentInterface $child) {
                    if ($child instanceof CompositeComponent &&
                        ! $child instanceof CompositeComponentWithEvents
                    ) {
                        return true;
                    }
                };

                $childs = $this->component->findChilds($callback, false);

                $this->assertCount(2, $childs);
                $this->assertSame($this->child3, $childs[0]);
                $this->assertSame($this->child4, $childs[1]);
            });
        });

        testCase('$parents = $child32->getParents();', function () {
            setUp(function () {
                $this->parents = $this->child32->getParents();
            });

            test('count($parents) == 2', function () {
                $this->assertCount(2, $this->parents);
            });

            test('$parents[0] === $child3', function () {
                $this->assertSame($this->child3, $this->parents[0]);
            });

            test('$parents[1] === $component', function () {
                $this->assertSame($this->component, $this->parents[1]);
            });
        });

        testCase('$parents = $child412->getParents();', function () {
            setUp(function () {
                $this->parents = $this->child412->getParents();
            });

            test('count($parents) == 3', function () {
                $this->assertCount(3, $this->parents);
            });

            test('$parents[0] === $child41', function () {
                $this->assertSame($this->child41, $this->parents[0]);
            });

            test('$parents[1] === $child4', function () {
                $this->assertSame($this->child4, $this->parents[1]);
            });

            test('$parents[2] === $component', function () {
                $this->assertSame($this->component, $this->parents[2]);
            });
        });

        testCase('$iterator = $child32->parents();', function () {
            setUpBeforeClassOnce(function () {
                $child32 = static::getVar('child32');
                static::setVar('iterator', $child32->parents());
            });

            test('iteration #1: $iterator->current() === $child3', function () {
                $this->assertSame($this->child3, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #2: $iterator->current() === $component', function () {
                $this->assertSame($this->component, $this->iterator->current());
                $this->iterator->next();
                $this->assertNull($this->iterator->current());
            });
        });

        testCase('$iterator = $child412->parents();', function () {
            setUpBeforeClassOnce(function () {
                $child412 = static::getVar('child412');
                static::setVar('iterator', $child412->parents());
            });

            test('iteration #1: $iterator->current() === $child41', function () {
                $this->assertSame($this->child41, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #2: $iterator->current() === $child4', function () {
                $this->assertSame($this->child4, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #3: $iterator->current() === $component', function () {
                $this->assertSame($this->component, $this->iterator->current());
                $this->iterator->next();
                $this->assertNull($this->iterator->current());
            });
        });

        testCase('exists a subtree listening for an event (see sources)', function () {
            setUp(function () {
                $this->eventName = uniqid('event');
                $this->event = new Event;
                $this->momentCapture1 = null;
                $this->momentCapture2 = null;
                $this->momentCapture3 = null;
                $this->moment = null;
                $this->momentBubble1 = null;
                $this->momentBubble2 = null;
                $this->momentBubble3 = null;

                $this->component->on($this->eventName, $this->listenerCapture1 = function () {
                    $this->momentCapture1 = new DateTime;
                }, true);

                $this->child4->on($this->eventName, $this->listenerCapture2 = function () {
                    $this->momentCapture2 = new DateTime;
                }, true);

                $this->child41->on($this->eventName, $this->listenerCapture3 = function () {
                    $this->momentCapture3 = new DateTime;
                }, true);

                $this->child412->on($this->eventName, $this->listener1 = function () {
                    $this->moment = new DateTime;
                });

                $this->child41->on($this->eventName, $this->listener2 = function () {
                    $this->momentBubble1 = new DateTime;
                });

                $this->child4->on($this->eventName, $this->listener3 = function () {
                    $this->momentBubble2 = new DateTime;
                });

                $this->component->on($this->eventName, $this->listener4 = function () {
                    $this->momentBubble3 = new DateTime;
                });
            });

            test('testing order of event propagation from capture to bubbling', function () {
                $this->child412->dispatchEvent($this->eventName, $this->event);

                $this->assertInstanceOf(DateTime::class, $this->momentCapture1);
                $this->assertInstanceOf(DateTime::class, $this->momentCapture2);
                $this->assertInstanceOf(DateTime::class, $this->momentCapture3);
                $this->assertInstanceOf(DateTime::class, $this->moment);
                $this->assertInstanceOf(DateTime::class, $this->momentBubble1);
                $this->assertInstanceOf(DateTime::class, $this->momentBubble2);
                $this->assertInstanceOf(DateTime::class, $this->momentBubble3);

                $this->assertGreaterThan($this->momentCapture1, $this->momentCapture2);
                $this->assertGreaterThan($this->momentCapture2, $this->momentCapture3);
                $this->assertGreaterThan($this->momentCapture3, $this->moment);
                $this->assertGreaterThan($this->moment, $this->momentBubble1);
                $this->assertGreaterThan($this->momentBubble1, $this->momentBubble2);
                $this->assertGreaterThan($this->momentBubble2, $this->momentBubble3);
            });

            test('testing order of event propagation when capture is disabled', function () {
                $this->child412->dispatchEvent($this->eventName, $this->event, false);

                $this->assertNull($this->momentCapture1);
                $this->assertNull($this->momentCapture2);
                $this->assertNull($this->momentCapture3);
                $this->assertGreaterThan($this->moment, $this->momentBubble1);
                $this->assertGreaterThan($this->momentBubble1, $this->momentBubble2);
                $this->assertGreaterThan($this->momentBubble2, $this->momentBubble3);
            });

            test('testing order of event propagation when bubbling is disabled', function () {
                $this->child412->dispatchEvent($this->eventName, $this->event, true, false);

                $this->assertGreaterThan($this->momentCapture1, $this->momentCapture2);
                $this->assertGreaterThan($this->momentCapture2, $this->momentCapture3);
                $this->assertGreaterThan($this->momentCapture3, $this->moment);
                $this->assertNull($this->momentBubble1);
                $this->assertNull($this->momentBubble2);
                $this->assertNull($this->momentBubble3);
            });

            test('testing order of event propagation when capture and bubbling are disabled', function () {
                $this->child412->dispatchEvent($this->eventName, $this->event, false, false);

                $this->assertNull($this->momentCapture1);
                $this->assertNull($this->momentCapture2);
                $this->assertNull($this->momentCapture3);
                $this->assertInstanceOf(DateTime::class, $this->moment);
                $this->assertNull($this->momentBubble1);
                $this->assertNull($this->momentBubble2);
                $this->assertNull($this->momentBubble3);
            });

            test('the event listeners are not executed when they are removed', function () {
                $this->component->off($this->eventName, $this->listenerCapture1, true);
                $this->child4->off($this->eventName, $this->listenerCapture2, true);
                $this->child41->off($this->eventName, $this->listenerCapture3, true);
                $this->child412->off($this->eventName, $this->listener1);
                $this->child41->off($this->eventName, $this->listener2);
                $this->child4->off($this->eventName, $this->listener3);
                $this->component->off($this->eventName, $this->listener4);

                $this->child412->dispatchEvent($this->eventName, $this->event);

                $this->assertNull($this->momentCapture1);
                $this->assertNull($this->momentCapture2);
                $this->assertNull($this->momentCapture3);
                $this->assertNull($this->moment);
                $this->assertNull($this->momentBubble1);
                $this->assertNull($this->momentBubble2);
                $this->assertNull($this->momentBubble3);
            });
        });
    });
});
