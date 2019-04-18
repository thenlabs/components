<?php

use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\CompositeComponentInterface;
use NubecuLabs\Components\ComponentWithNameInterface;
use NubecuLabs\Components\ComponentTrait;
use NubecuLabs\Components\CompositeComponentTrait;
use NubecuLabs\Components\ComponentWithNameTrait;
use NubecuLabs\Components\SearchByNameTrait;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('ComponentWithNameTest.php', function () {
    createMacro('tests', function () {
        test('$component->getName() === null', function () {
            $this->assertNull($this->component->getName());
        });

        $name = uniqid('comp');
        testCase("\$component->setName('$name')", function () use ($name) {
            setUp(function () use ($name) {
                $this->component->setName($name);
            });

            test("\$component->getName() === '$name'", function () use ($name) {
                $this->assertEquals($name, $this->component->getName());
            });
        });
    });

    testCase('$component = new Component;', function () {
        setUp(function () {
            $this->component = new class implements ComponentInterface, ComponentWithNameInterface {
                use ComponentTrait, ComponentWithNameTrait;
            };
        });

        useMacro('tests');
    });

    testCase('$component = new CompositeComponent;', function () {
        setUp(function () {
            $this->component = new class implements CompositeComponentInterface, ComponentWithNameInterface {
                use CompositeComponentTrait, ComponentWithNameTrait;
            };
        });

        useMacro('tests');
    });

    testCase('exists a components tree with named components (see sources)', function () {
        /**
         * C:    Component
         * CC:   CompositeComponent
         *
         * $root (CC)
         *    |
         *    |____$child1 (CC)
         *    |        |
         *    |        |_____$child11 (CC)(N)
         *    |        |_____$child12 (C)(N)
         *    |
         *    |____$child2 (CC)
         *    |____$child3 (C)
         *    |____$child4 (C)(N)
         */

        $name1 = uniqid('name1');
        $name2 = uniqid('name2');

        setUp(function () use ($name1, $name2) {
            $this->root = new class implements CompositeComponentInterface {
                use CompositeComponentTrait, SearchByNameTrait;
            };

            $this->child1 = new class implements CompositeComponentInterface {
                use CompositeComponentTrait;
            };

            $this->child11 = new class implements CompositeComponentInterface, ComponentWithNameInterface {
                use CompositeComponentTrait, ComponentWithNameTrait;
            };

            $this->child12 = new class implements ComponentInterface, ComponentWithNameInterface {
                use ComponentTrait, ComponentWithNameTrait;
            };

            $this->child2 = new class implements CompositeComponentInterface {
                use CompositeComponentTrait;
            };

            $this->child3 = new class implements ComponentInterface {
                use ComponentTrait, ComponentWithNameTrait;
            };

            $this->child4 = new class implements ComponentInterface, ComponentWithNameInterface {
                use ComponentTrait, ComponentWithNameTrait;
            };

            $this->child3->setName($name1);
            $this->child4->setName($name1);

            $this->child11->setName($name2);
            $this->child12->setName($name2);

            $this->child1->addChilds($this->child11, $this->child12);
            $this->root->addChilds($this->child1, $this->child2, $this->child3, $this->child4);
        });

        // the child3 is not found because not implements the ComponentWithNameInterface.
        test("\$root->findChildByName('$name1') === \$child4", function () use ($name1) {
            $this->assertSame($this->child4, $this->root->findChildByName($name1));
        });

        test("\$root->findChildByName('$name2') === \$child11", function () use ($name2) {
            $this->assertSame($this->child11, $this->root->findChildByName($name2));
        });

        test("\$root->findChildsByName('$name2')[0] === \$child11", function () use ($name2) {
            $this->assertSame($this->child11, $this->root->findChildsByName($name2)[0]);
        });

        test("\$root->findChildsByName('$name2')[1] === \$child12", function () use ($name2) {
            $this->assertSame($this->child12, $this->root->findChildsByName($name2)[1]);
        });
    });

    testCase('exception when SearchByNameTrait is used in a class that is not CompositeComponentInterface', function () {
        setUp(function () {
            $this->expectException(Exception::class);
            $this->expectExceptionMessage('The SearchByNameTrait only can be used on a class that implements the CompositeComponentInterface.');
        });

        testCase(function () {
            setUp(function () {
                $this->entity = new class implements ComponentInterface {
                    use ComponentTrait, SearchByNameTrait;
                };
            });

            test(function () {
                $this->entity->findChildByName(uniqid());
            });

            test(function () {
                $this->entity->findChildsByName(uniqid());
            });
        });
    });
});
