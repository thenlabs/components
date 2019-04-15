<?php

use NubecuLabs\Components\Tests\Entity\CompositeComponent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use NubecuLabs\Components\Tests\Entity\Component;
use NubecuLabs\Components\Exception\InvalidChildException;
use NubecuLabs\Components\ComponentInterface;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('CompositeComponentTest.php', function () {
    setUp(function () {
        $this->componentClass = CompositeComponent::class;
    });

    testCase(sprintf("\$component = new \\%s;", CompositeComponent::class), function () {
        useMacro('commons');

        testCase('$component->getId();', function () {
            test('returns an unique string that starts with "compositecomponent_"', function () {
                $id = $this->component->getId();

                $this->assertGreaterThan(13, strlen($id));
                $this->assertStringStartsWith('compositecomponent_', $id);
            });
        });

        testCase('$component->getCaptureEventDispatcher();', function () {
            test('returns an instance of "Symfony\Component\EventDispatcher\EventDispatcher"', function () {
                $this->assertInstanceOf(EventDispatcher::class, $this->component->getCaptureEventDispatcher());
            });

            test('returns always the same instance', function () {
                $dispatcher = $this->component->getCaptureEventDispatcher();

                $this->assertSame($dispatcher, $this->component->getCaptureEventDispatcher());
                $this->assertSame($dispatcher, $this->component->getCaptureEventDispatcher());
            });

            testCase('$component->setCaptureEventDispatcher($newDispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher);', function () {
                test('$component->getCaptureEventDispatcher() === $newDispatcher', function () {
                    $newDispatcher = new EventDispatcher;

                    $this->component->setCaptureEventDispatcher($newDispatcher);

                    $this->assertSame($newDispatcher, $this->component->getCaptureEventDispatcher());
                });
            });
        });

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
            testCase('it is invoked to $component->validateChild($child)', function () {
                createMethod('addTheChild', function () {
                    $component = $this->getMockBuilder($this->componentClass)
                        ->setMethods(['validateChild'])
                        ->getMock();
                    $component->expects($this->once())
                        ->method('validateChild')
                        ->with($this->equalTo($this->child))
                        ->willReturn(true)
                    ;

                    $component->addChild($this->child);
                });

                test('when child is an instance of ComponentInterface', function () {
                    $this->child = new Component;

                    $this->addTheChild();
                });

                test('when child is an instance of CompositeComponentInterface', function () {
                    $this->child = new CompositeComponent;

                    $this->addTheChild();
                });
            });

            testCase('it is triggered a "NubecuLabs\Components\Exception\InvalidChildException" when #validateChild() returns false', function () {
                setUp(function () {
                    $this->component = $this->getMockBuilder($this->componentClass)
                        ->setMethods(['validateChild'])
                        ->getMock();
                    $this->component->expects($this->once())
                        ->method('validateChild')
                        ->willReturn(false)
                    ;

                    $this->expectException(InvalidChildException::class);
                });

                createMethod('addTheChild', function () {
                    $this->expectExceptionMessage("Invalid child with id equal to '{$this->child->getId()}'.");

                    $this->component->addChild($this->child);
                });

                test('when child is an instance of ComponentInterface', function () {
                    $this->child = new Component;

                    $this->addTheChild();
                });

                test('when child is an instance of CompositeComponentInterface', function () {
                    $this->child = new CompositeComponent;

                    $this->addTheChild();
                });
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

            testCase(sprintf('$component->addChild($child2 = new %s);', CompositeComponent::class), function () {
                setUp(function () {
                    $this->child2 = new CompositeComponent;
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

        testCase(sprintf('$component->addChild($child = new %s);', CompositeComponent::class), function () {
            setUp(function () {
                $this->child = new CompositeComponent;
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

        testCase(sprintf('$component->addChild($child = new %s, false);', CompositeComponent::class), function () {
            setUp(function () {
                $this->child = new CompositeComponent;
                $this->component->addChild($this->child, false);
            });

            useMacro('commons for when the component adds a child without assign the parent');
        });

        testCase('exists four components', function () {
            setUp(function () {
                $this->child1 = new Component;
                $this->child2 = new Component;
                $this->child3 = new CompositeComponent;
                $this->child4 = new CompositeComponent;
            });

            testCase('$component->addChilds($child1, $child2, $child3, $child4);', function () {
                setUp(function () {
                    $component = $this->getMockBuilder($this->componentClass)
                        ->setMethods(['addChild'])
                        ->getMock();
                    $component->expects($this->exactly(4))
                        ->method('addChild')
                        ->withConsecutive(
                            [$this->child1],
                            [$this->child2],
                            [$this->child3],
                            [$this->child4]
                        )
                    ;

                    $component->addChilds($this->child1, $this->child2, $this->child3, $this->child4);
                });

                test('invoke to $component->addChild($child1) at the first time', function () {
                    $this->assertTrue(true);
                });

                test('invoke to $component->addChild($child2) at the second time', function () {
                    $this->assertTrue(true);
                });

                test('invoke to $component->addChild($child3) at the third time', function () {
                    $this->assertTrue(true);
                });

                test('invoke to $component->addChild($child4) at the fourth time', function () {
                    $this->assertTrue(true);
                });
            });
        });
    });
});
