<?php

use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\Tests\Component;
use NubecuLabs\Components\Tests\CompositeComponent;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('CompositeComponentTest.php', function () {
    createMethod('getNewComponent', function () {
        return new CompositeComponent;
    });

    testCase(sprintf('$component = new \\%s;', CompositeComponent::class), function () {
        useMacro('common tests for ComponentTrait and CompositeComponentTrait');

        $id = uniqid();
        test("\$component->hasChild('$id') === false", function() use ($id) {
            $this->assertFalse($this->component->hasChild($id));
        });

        test(sprintf('$component->hasChild(new %s) === false', Component::class), function() use ($id) {
            $child = $this->createMock(ComponentInterface::class);

            $this->assertFalse($this->component->hasChild($child));
        });

        testCase('$component->getId();', function () {
            test('returns an unique string that starts with "compositecomponent_"', function () {
                $id = $this->component->getId();

                $this->assertGreaterThan(13, strlen($id));
                $this->assertStringStartsWith('compositecomponent_', $id);
            });
        });

        testCase(sprintf('$component->addChild($child = new %s);', Component::class), function () {
            setUp(function () {
                $this->child = new Component;
                $this->component->addChild($this->child);
            });

            test('$component->hasChild($child) === true', function() {
                $this->assertTrue($this->component->hasChild($this->child));
            });

            test('$component->hasChild($child->getId()) === true', function() {
                $this->assertTrue($this->component->hasChild($this->child->getId()));
            });

            test('$child->getParent() === $component', function() {
                $this->assertEquals($this->component, $this->child->getParent());
            });

            testCase(sprintf('$component->addChild($child2 = new %s);', Component::class), function () {
                setUp(function () {
                    $this->child2 = new Component;
                    $this->component->addChild($this->child2);
                });

                test('$component->hasChild($child2) === true', function() {
                    $this->assertTrue($this->component->hasChild($this->child2));
                });

                test('$component->hasChild($child2->getId()) === true', function() {
                    $this->assertTrue($this->component->hasChild($this->child2->getId()));
                });

                test('$child2->getParent() === $component', function() {
                    $this->assertEquals($this->component, $this->child2->getParent());
                });

                testCase('$children = $component->getChildren();', function () {
                    setUp(function () {
                        $this->children = $this->component->getChildren();
                    });

                    test('count($children) == 2', function() {
                        $this->assertCount(2, $this->children);
                    });

                    test('$children[$child->getId()] === $child', function() {
                        $this->assertEquals($this->child, $this->children[$this->child->getId()]);
                    });

                    test('$children[$child2->getId()] === $child2', function() {
                        $this->assertEquals($this->child2, $this->children[$this->child2->getId()]);
                    });

                    createMacro('drop child tests', function () {
                        test('$child->getParent() === null', function() {
                            $this->assertNull($this->child->getParent());
                        });

                        test('$component->hasChild($child) === false', function() {
                            $this->assertFalse($this->component->hasChild($this->child));
                        });

                        test('$component->hasChild($child->getId()) === false', function() {
                            $this->assertFalse($this->component->hasChild($this->child->getId()));
                        });

                        testCase('$children = $component->getChildren();', function () {
                            setUp(function () {
                                $this->children = $this->component->getChildren();
                            });

                            test('count($children) == 1', function() {
                                $this->assertCount(1, $this->children);
                            });

                            test('$children[$child2->getId()] === $child2', function() {
                                $this->assertEquals($this->child2, $this->children[$this->child2->getId()]);
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

            test('$component->hasChild($child) === true', function() {
                $this->assertTrue($this->component->hasChild($this->child));
            });

            test('$component->hasChild($child->getId()) === true', function() {
                $this->assertTrue($this->component->hasChild($this->child->getId()));
            });

            test('$child->getParent() === null', function() {
                $this->assertNull($this->child->getParent());
            });
        });
    });

    testCase('exists the tree of components defined in this file (see sources)', function () {
        /**
         * $component
         *     |
         *     |____$child1
         *     |____$child2
         *     |       |
         *     |       |____$child3
         *     |       |____$child4
         *     |
         *     |____$child5
         *     |       |
         *     |       |____$child6
         *     |               |____$child7
         *     |               |       |____$child8
         *     |               |       |____$child9
         *     |               |
         *     |               |____$child10
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
                'component', 'child1', 'child2', 'child3', 'child4', 'child5',
                'child6', 'child7', 'child8', 'child9', 'child10'
            ));
        });

        setUp(function () {
            $this->injectVars();
        });

        testCase('$iterator = $component->children();', function () {
            setUpBeforeClassOnce(function () {
                $component = static::getVar('component');
                static::setVar('iterator', $component->children());
            });

            test('iteration #1: $iterator->current() === $child1', function() {
                $this->assertEquals($this->child1, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #2: $iterator->current() === $child2', function() {
                $this->assertEquals($this->child2, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #3: $iterator->current() === $child3', function() {
                $this->assertEquals($this->child3, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #4: $iterator->current() === $child4', function() {
                $this->assertEquals($this->child4, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #5: $iterator->current() === $child5', function() {
                $this->assertEquals($this->child5, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #6: $iterator->current() === $child6', function() {
                $this->assertEquals($this->child6, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #7: $iterator->current() === $child7', function() {
                $this->assertEquals($this->child7, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #8: $iterator->current() === $child8', function() {
                $this->assertEquals($this->child8, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #9: $iterator->current() === $child9', function() {
                $this->assertEquals($this->child9, $this->iterator->current());
                $this->iterator->next();
            });

            test('iteration #10: $iterator->current() === $child10', function() {
                $this->assertEquals($this->child10, $this->iterator->current());
                $this->iterator->next();
            });
        });
    });
});
