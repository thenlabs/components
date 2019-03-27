<?php

use NubecuLabs\Components\CompositeComponentTrait;
use NubecuLabs\Components\Tests\CompositeComponent;

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
});
