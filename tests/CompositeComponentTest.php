<?php

use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\Tests\Component;
use NubecuLabs\Components\Tests\CompositeComponent;
use Symfony\Component\EventDispatcher\EventDispatcher;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('CompositeComponentTest.php', function () {
    createMethod('getNewComponent', function () {
        return new CompositeComponent;
    });

    testCase(sprintf('$component = new \\%s;', CompositeComponent::class), function () {
        useMacro('common tests for ComponentTrait and CompositeComponentTrait');

        $id = uniqid();
        test("\$component->hasChild('$id') === false", function () use ($id) {
            $this->assertFalse($this->component->hasChild($id));
        });

        test(sprintf('$component->hasChild(new %s) === false', Component::class), function () use ($id) {
            $child = $this->createMock(ComponentInterface::class);

            $this->assertFalse($this->component->hasChild($child));
        });

        test("\$component->getChild('$id') === null", function () use ($id) {
            $this->assertNull($this->component->getChild($id));
        });

        testCase('$component->getId();', function () {
            test('returns an unique string that starts with "compositecomponent_"', function () {
                $id = $this->component->getId();

                $this->assertGreaterThan(13, strlen($id));
                $this->assertStringStartsWith('compositecomponent_', $id);
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

        testCase(sprintf('$component->addChild($child = new %s);', Component::class), function () {
            setUp(function () {
                $this->child = new Component;
                $this->component->addChild($this->child);
            });

            test('$component->hasChild($child) === true', function () {
                $this->assertTrue($this->component->hasChild($this->child));
            });

            test('$component->hasChild($child->getId()) === true', function () {
                $this->assertTrue($this->component->hasChild($this->child->getId()));
            });

            test('$child->getParent() === $component', function () {
                $this->assertSame($this->component, $this->child->getParent());
            });

            test('$component->getChild($child->getId()) === $child', function () {
                $this->assertSame($this->child, $this->component->getChild($this->child->getId()));
            });

            testCase(sprintf('$component->addChild($child2 = new %s);', Component::class), function () {
                setUp(function () {
                    $this->child2 = new Component;
                    $this->component->addChild($this->child2);
                });

                test('$component->hasChild($child2) === true', function () {
                    $this->assertTrue($this->component->hasChild($this->child2));
                });

                test('$component->hasChild($child2->getId()) === true', function () {
                    $this->assertTrue($this->component->hasChild($this->child2->getId()));
                });

                test('$child2->getParent() === $component', function () {
                    $this->assertSame($this->component, $this->child2->getParent());
                });

                testCase('$children = $component->getChilds();', function () {
                    setUp(function () {
                        $this->children = $this->component->getChilds();
                    });

                    test('count($children) == 2', function () {
                        $this->assertCount(2, $this->children);
                    });

                    test('$children[$child->getId()] === $child', function () {
                        $this->assertSame($this->child, $this->children[$this->child->getId()]);
                    });

                    test('$children[$child2->getId()] === $child2', function () {
                        $this->assertSame($this->child2, $this->children[$this->child2->getId()]);
                    });

                    createMacro('drop child tests', function () {
                        test('$child->getParent() === null', function () {
                            $this->assertNull($this->child->getParent());
                        });

                        test('$component->hasChild($child) === false', function () {
                            $this->assertFalse($this->component->hasChild($this->child));
                        });

                        test('$component->hasChild($child->getId()) === false', function () {
                            $this->assertFalse($this->component->hasChild($this->child->getId()));
                        });

                        testCase('$children = $component->getChilds();', function () {
                            setUp(function () {
                                $this->children = $this->component->getChilds();
                            });

                            test('count($children) == 1', function () {
                                $this->assertCount(1, $this->children);
                            });

                            test('$children[$child2->getId()] === $child2', function () {
                                $this->assertSame($this->child2, $this->children[$this->child2->getId()]);
                            });
                        });
                    });

                    testCase('$this->component->dropChild($this->child);', function () {
                        setUp(function () {
                            $this->component->dropChild($this->child);
                        });

                        useMacro('drop child tests');
                    });

                    testCase('$this->component->dropChild($this->child->getId());', function () {
                        setUp(function () {
                            $this->component->dropChild($this->child->getId());
                        });

                        useMacro('drop child tests');
                    });
                });
            });
        });

        testCase(sprintf('$component->addChild($child = new %s, false);', Component::class), function () {
            setUp(function () {
                $this->child = new Component;
                $this->component->addChild($this->child, false);
            });

            test('$component->hasChild($child) === true', function () {
                $this->assertTrue($this->component->hasChild($this->child));
            });

            test('$component->hasChild($child->getId()) === true', function () {
                $this->assertTrue($this->component->hasChild($this->child->getId()));
            });

            test('$child->getParent() === null', function () {
                $this->assertNull($this->child->getParent());
            });
        });
    });

    testCase('exists a tree of components (see sources)', function () {
        /**
         * C:  Component
         * CC: CompositeComponent
         *
         * $component
         *     |
         *     |____$child1 (C)
         *     |____$child2 (CC)
         *     |       |
         *     |       |____$child3 (CC)
         *     |       |____$child4 (C)
         *     |
         *     |____$child5 (CC)
         *     |       |
         *     |       |____$child6 (CC)
         *     |               |____$child7 (CC)
         *     |               |       |____$child8 (CC)
         *     |               |       |____$child9 (CC)
         *     |               |
         *     |               |____$child10 (CC)
         */
        setUpBeforeClassOnce(function () {
            $child1 = new Component('child1');

            $child3 = new CompositeComponent('child3');
            $child4 = new Component('child4');

            $child2 = new CompositeComponent('child2');
            $child2->addChild($child3);
            $child2->addChild($child4);

            $child8 = new CompositeComponent('child8');
            $child9 = new CompositeComponent('child9');
            $child10 = new CompositeComponent('child10');

            $child7 = new CompositeComponent('child7');
            $child7->addChild($child8);
            $child7->addChild($child9);

            $child6 = new CompositeComponent('child6');
            $child6->addChild($child7);
            $child6->addChild($child10);

            $child5 = new CompositeComponent('child5');
            $child5->addChild($child6);

            $component = new CompositeComponent('component');
            $component->addChild($child1);
            $component->addChild($child2);
            $component->addChild($child5);

            static::addVars(compact(
                'component',
                'child1',
                'child2',
                'child3',
                'child4',
                'child5',
                'child6',
                'child7',
                'child8',
                'child9',
                'child10'
            ));
        });

        setUp(function () {
            $this->injectVars();
        });

        $id = uniqid('comp');
        test("\$component->findChildById('$id') === null", function () use ($id) {
            $this->assertNull($this->component->findChildById($id));
        });

        test('$component->findChildById("child4") === $child4', function () {
            $this->assertSame($this->child4, $this->component->findChildById('child4'));
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

            test('iteration #4: $iterator->current() === $child4', function () {
                $this->assertSame($this->child4, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #5: $iterator->current() === $child5', function () {
                $this->assertSame($this->child5, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #6: $iterator->current() === $child6', function () {
                $this->assertSame($this->child6, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #7: $iterator->current() === $child7', function () {
                $this->assertSame($this->child7, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #8: $iterator->current() === $child8', function () {
                $this->assertSame($this->child8, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #9: $iterator->current() === $child9', function () {
                $this->assertSame($this->child9, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #10 (End): $iterator->current() === $child10', function () {
                $this->assertSame($this->child10, $this->iterator->current());
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

            test('iteration #3 (End): $iterator->current() === $child5', function () {
                $this->assertSame($this->child5, $this->iterator->current());
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

            test('returns the child for whom the callback returns true', function () {
                $this->assertSame($this->child4, $this->component->findChild(function (ComponentInterface $child) {
                    return $child->getId() == 'child4' ? true : false;
                }));
            });

            test('$component->findChild($callback, false) does not do a recursive search', function () {
                $callback = function (ComponentInterface $child) {
                    return $child->getId() == 'child4' ? true : false;
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

            test('returns in an array the childs for whom the callback returns true', function () {
                $callback = function (ComponentInterface $child) {
                    return $child instanceof CompositeComponent ? false : true;
                };

                $childs = $this->component->findChilds($callback);

                $this->assertCount(2, $childs);
                $this->assertSame($this->child1, $childs[0]);
                $this->assertSame($this->child4, $childs[1]);
            });

            test('$component->findChilds($callback, false) does not do a recursive search', function () {
                $callback = function (ComponentInterface $child) {
                    return $child instanceof CompositeComponent ? false : true;
                };

                $childs = $this->component->findChilds($callback, false);

                $this->assertCount(1, $childs);
                $this->assertSame($this->child1, $childs[0]);
            });
        });

        testCase('$parents = $child1->getParents();', function () {
            setUp(function () {
                $this->parents = $this->child1->getParents();
            });

            test('count($parents) == 1', function () {
                $this->assertCount(1, $this->parents);
            });

            test('$parents[0] === $component', function () {
                $this->assertSame($this->component, $this->parents[0]);
            });
        });

        testCase('$parents = $child8->getParents();', function () {
            setUp(function () {
                $this->parents = $this->child8->getParents();
            });

            test('count($parents) == 4', function () {
                $this->assertCount(4, $this->parents);
            });

            test('$parents[0] === $child7', function () {
                $this->assertSame($this->child7, $this->parents[0]);
            });

            test('$parents[1] === $child6', function () {
                $this->assertSame($this->child6, $this->parents[1]);
            });

            test('$parents[2] === $child5', function () {
                $this->assertSame($this->child5, $this->parents[2]);
            });

            test('$parents[3] === $component', function () {
                $this->assertSame($this->component, $this->parents[3]);
            });
        });
    });
});
