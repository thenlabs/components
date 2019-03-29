<?php

use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\CompositeComponentInterface;
use NubecuLabs\Components\CompositeComponentTrait;
use NubecuLabs\Components\Tests\Component;
use NubecuLabs\Components\Tests\CompositeComponent;
use NubecuLabs\Components\Exception\InvalidChildException;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('CompositeComponentTraitTest.php', function () {
    testCase('#getOwnDependencies()', function () {
        test('returns an empty array by default', function () {
            $trait = $this->getMockForTrait(CompositeComponentTrait::class);

            $this->assertEquals([], $trait->getOwnDependencies());
        });

        test('returns the #dependencies data', function () {
            $expectedData = range(1, mt_rand(1, 10)); // random array
            $component = new CompositeComponent;
            $component->setDependencies($expectedData);

            $this->assertEquals($expectedData, $component->getOwnDependencies());
        });
    });

    testCase('it is triggered a "NubecuLabs\Components\Exception\InvalidChildException" when #validateChild() returns false', function () {
        setUp(function () {
            $this->parent = new class implements CompositeComponentInterface {
                use CompositeComponentTrait;

                public function validateChild(ComponentInterface $child): bool
                {
                    return $child instanceof CompositeComponentInterface ? true : false;
                }
            };

            $this->child = new Component;
            $this->expectException(InvalidChildException::class);
            $this->expectExceptionMessage("Invalid child with id equal to '{$this->child->getId()}'.");
        });

        test('case: $parent->addChild($child);', function () {
            $this->parent->addChild($this->child);
        });

        test('case: $child->setParent($parent);', function () {
            $this->child->setParent($this->parent);
        });
    });
});
