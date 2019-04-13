<?php

use NubecuLabs\Components\Tests\Entity\Component;
use NubecuLabs\Components\Tests\Entity\ComponentWithEvents;
use NubecuLabs\Components\Tests\Entity\CompositeComponent;
use NubecuLabs\Components\Tests\Entity\CompositeComponentWithEvents;
use NubecuLabs\Components\ComponentInterface;

createMacro('commons of CompositeComponent and CompositeComponentWithEvents', function () {
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

    createMacro('commons for when the component adds a child', function () {
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

        createMacro('tests for when the component adds other child', function () {
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

        testCase(sprintf('$component->addChild($child2 = new %s);', Component::class), function () {
            setUp(function () {
                $this->child2 = new Component;
                $this->component->addChild($this->child2);
            });

            useMacro('tests for when the component adds other child');
        });

        testCase(sprintf('$component->addChild($child2 = new %s);', ComponentWithEvents::class), function () {
            setUp(function () {
                $this->child2 = new ComponentWithEvents;
                $this->component->addChild($this->child2);
            });

            useMacro('tests for when the component adds other child');
        });

        testCase(sprintf('$component->addChild($child2 = new %s);', CompositeComponent::class), function () {
            setUp(function () {
                $this->child2 = new CompositeComponent;
                $this->component->addChild($this->child2);
            });

            useMacro('tests for when the component adds other child');
        });

        testCase(sprintf('$component->addChild($child2 = new %s);', CompositeComponentWithEvents::class), function () {
            setUp(function () {
                $this->child2 = new CompositeComponentWithEvents;
                $this->component->addChild($this->child2);
            });

            useMacro('tests for when the component adds other child');
        });
    });

    testCase(sprintf('$component->addChild($child = new %s);', Component::class), function () {
        setUp(function () {
            $this->child = new Component;
            $this->component->addChild($this->child);
        });

        useMacro('commons for when the component adds a child');
    });

    testCase(sprintf('$component->addChild($child = new %s);', ComponentWithEvents::class), function () {
        setUp(function () {
            $this->child = new ComponentWithEvents;
            $this->component->addChild($this->child);
        });

        useMacro('commons for when the component adds a child');
    });

    testCase(sprintf('$component->addChild($child = new %s);', CompositeComponent::class), function () {
        setUp(function () {
            $this->child = new CompositeComponent;
            $this->component->addChild($this->child);
        });

        useMacro('commons for when the component adds a child');
    });

    testCase(sprintf('$component->addChild($child = new %s);', CompositeComponentWithEvents::class), function () {
        setUp(function () {
            $this->child = new CompositeComponentWithEvents;
            $this->component->addChild($this->child);
        });

        useMacro('commons for when the component adds a child');
    });

    createMacro('commons for when the component adds a child without assign the parent', function () {
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

    testCase(sprintf('$component->addChild($child = new %s, false);', Component::class), function () {
        setUp(function () {
            $this->child = new Component;
            $this->component->addChild($this->child, false);
        });

        useMacro('commons for when the component adds a child without assign the parent');
    });

    testCase(sprintf('$component->addChild($child = new %s, false);', ComponentWithEvents::class), function () {
        setUp(function () {
            $this->child = new ComponentWithEvents;
            $this->component->addChild($this->child, false);
        });

        useMacro('commons for when the component adds a child without assign the parent');
    });

    testCase(sprintf('$component->addChild($child = new %s, false);', CompositeComponent::class), function () {
        setUp(function () {
            $this->child = new CompositeComponent;
            $this->component->addChild($this->child, false);
        });

        useMacro('commons for when the component adds a child without assign the parent');
    });

    testCase(sprintf('$component->addChild($child = new %s, false);', CompositeComponentWithEvents::class), function () {
        setUp(function () {
            $this->child = new CompositeComponentWithEvents;
            $this->component->addChild($this->child, false);
        });

        useMacro('commons for when the component adds a child without assign the parent');
    });
});
