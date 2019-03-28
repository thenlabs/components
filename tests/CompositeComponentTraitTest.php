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

    testCase('#validateChild()', function () {
        test(function () {
            $parent = new class implements CompositeComponentInterface {
                use CompositeComponentTrait;

                public function validateChild(ComponentInterface $child): bool
                {
                    return $child instanceof CompositeComponentInterface ? true : false;
                }
            };

            $child = new Component;
            $this->expectException(InvalidChildException::class);
            $this->expectExceptionMessage("Invalid child with id equal to '{$child->getId()}'.");

            $parent->addChild($child);
        });
    });
});
