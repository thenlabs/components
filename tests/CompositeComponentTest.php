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
});
