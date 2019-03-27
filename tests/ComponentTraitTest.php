<?php

use NubecuLabs\Components\ComponentTrait;
use NubecuLabs\Components\Tests\Component;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('ComponentTraitTest.php', function () {
    test('#getDependencies() returns an empty array by default', function () {
        $trait = $this->getMockForTrait(ComponentTrait::class);

        $this->assertEquals([], $trait->getDependencies());
    });

    test('#getDependencies() returns the #dependencies data', function () {
        $expectedData = range(1, mt_rand(1, 10)); // random array
        $component = new Component;
        $component->setDependencies($expectedData);

        $this->assertEquals($expectedData, $component->getDependencies());
    });
});
