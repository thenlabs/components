<?php

use NubecuLabs\Components\ComponentTrait;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('ComponentTraitTest.php', function () {
    test('#getDependencies() returns an empty array', function() {
        $trait = $this->getMockForTrait(ComponentTrait::class);

        $this->assertEquals([], $trait->getDependencies());
    });
});
