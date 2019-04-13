<?php

use NubecuLabs\Components\Tests\Entity\Component;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('ComponentTest.php', function () {
    setUp(function () {
        $this->componentClass = Component::class;
    });

    testCase(sprintf("\$component = new \\%s;", Component::class), function () {
        useMacro('commons');

        testCase('$component->getId();', function () {
            test('returns an unique string that starts with "component_"', function () {
                $id = $this->component->getId();

                $this->assertGreaterThan(13, strlen($id));
                $this->assertStringStartsWith('component_', $id);
            });
        });
    });
});
