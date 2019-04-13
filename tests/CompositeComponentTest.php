<?php

use NubecuLabs\Components\Tests\Entity\Component;
use NubecuLabs\Components\Tests\Entity\CompositeComponent;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('CompositeComponentTest.php', function () {
    createMethod('getNewComponent', function () {
        return new CompositeComponent;
    });

    testCase(sprintf("\$component = new \\%s;", CompositeComponent::class), function () {
        useMacro('commons');

        useMacro('commons of CompositeComponent and CompositeComponentWithEvents');

        testCase('$component->getId();', function () {
            test('returns an unique string that starts with "compositecomponent_"', function () {
                $id = $this->component->getId();

                $this->assertGreaterThan(13, strlen($id));
                $this->assertStringStartsWith('compositecomponent_', $id);
            });
        });
    });
});
