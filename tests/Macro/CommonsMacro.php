<?php

use NubecuLabs\Components\Tests\Entity\CompositeComponent;
use NubecuLabs\Components\Tests\Entity\CompositeComponentWithEvents;

createMacro('commons', function () {
    setUp(function () {
        $this->component = $this->getNewComponent();
    });

    test('$component->getParent() === null', function () {
        $this->assertNull($this->component->getParent());
    });

    test('$component->getOwnDependencies() === []', function () {
        $this->assertSame([], $this->component->getOwnDependencies());
    });

    testCase('$component->getId();', function () {
        test('always returns the same value', function () {
            $id = $this->component->getId();

            $this->assertEquals($id, $this->component->getId());
            $this->assertEquals($id, $this->component->getId());
            $this->assertEquals($id, $this->component->getId());
        });
    });

    testCase('$component->setParent(null);', function () {
        test('$component->getParent() === null', function () {
            $this->component->setParent(null);

            $this->assertNull($this->component->getParent());
        });
    });

    createMacro('tests for when the parent is assigned', function () {
        test('$component->getParent() === $parent', function () {
            $this->assertEquals($this->parent, $this->component->getParent());
        });

        test('$parent->hasChild($component) === true', function () {
            $this->assertTrue($this->parent->hasChild($this->component));
        });

        createMacro('remove the parent tests', function () {
            test('$component->getParent() === null', function () {
                $this->assertNull($this->component->getParent());
            });

            test('$parent->hasChild($component) === false', function () {
                $this->assertFalse($this->parent->hasChild($this->component));
            });
        });

        testCase('$component->setParent(null);', function () {
            setUp(function () {
                $this->component->setParent(null);
            });

            useMacro('remove the parent tests');
        });

        createMacro('tests for when a new parent is assigned', function () {
            test('$parent->hasChild($component) === false', function () {
                $this->assertFalse($this->parent->hasChild($this->component));
            });

            test('$parent2->hasChild($component) === true', function () {
                $this->assertTrue($this->parent2->hasChild($this->component));
            });

            test('$component->getParent() === $parent2', function () {
                $this->assertEquals($this->parent2, $this->component->getParent());
            });
        });

        testCase('$component->setParent($parent2 = new \NubecuLabs\Components\Tests\Entity\CompositeComponent);', function () {
            setUp(function () {
                $this->parent2 = new CompositeComponent;
                $this->component->setParent($this->parent2);
            });

            useMacro('tests for when a new parent is assigned');
        });

        testCase('$component->setParent($parent2 = new \NubecuLabs\Components\Tests\Entity\CompositeComponentWithEvents);', function () {
            setUp(function () {
                $this->parent2 = new CompositeComponentWithEvents;
                $this->component->setParent($this->parent2);
            });

            useMacro('tests for when a new parent is assigned');
        });
    });

    testCase('$component->setParent($parent = new \NubecuLabs\Components\Tests\Entity\CompositeComponent);', function () {
        setUp(function () {
            $this->parent = new CompositeComponent;
            $this->component->setParent($this->parent);
        });

        useMacro('tests for when the parent is assigned');
    });

    testCase('$component->setParent($parent = new \NubecuLabs\Components\Tests\Entity\CompositeComponentWithEvents);', function () {
        setUp(function () {
            $this->parent = new CompositeComponentWithEvents;
            $this->component->setParent($this->parent);
        });

        useMacro('tests for when the parent is assigned');
    });

    createMacro('tests for when the parent is assigned without add the child in the parent', function () {
        test('$component->getParent() === $parent', function () {
            $this->assertEquals($this->parent, $this->component->getParent());
        });

        test('$parent->hasChild($component) === false', function () {
            $this->assertFalse($this->parent->hasChild($this->component));
        });
    });

    testCase('$component->setParent($parent = new \NubecuLabs\Components\Tests\Entity\CompositeComponent, false);', function () {
        setUp(function () {
            $this->parent = new CompositeComponent;
            $this->component->setParent($this->parent, false);
        });

        useMacro('tests for when the parent is assigned without add the child in the parent');
    });

    testCase('$component->setParent($parent = new \NubecuLabs\Components\Tests\Entity\CompositeWithEventsComponent, false);', function () {
        setUp(function () {
            $this->parent = new CompositeComponentWithEvents;
            $this->component->setParent($this->parent, false);
        });

        useMacro('tests for when the parent is assigned without add the child in the parent');
    });

    testCase('$iterator = $component->parents();', function () {
        test('the iterator is empty', function () {
            $iterator = $this->component->parents();

            $this->assertNull($iterator->current());
        });
    });
});
