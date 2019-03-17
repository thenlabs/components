<?php

use NubecuLabs\Components\Tests\Component;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('ComponentTest.php', function () {
    createMethod('getNewComponent', function () {
        return new Component;
    });

    testCase(sprintf("\$component = new \\%s;", Component::class), function () {
        testCase('$component->getId()', function () {
            test('returns an unique string that starts with "component_"', function () {
                $id = $this->component->getId();

                $this->assertGreaterThan(13, strlen($id));
                $this->assertStringStartsWith('component_', $id);
            });
        });

        useMacro('common tests for ComponentTrait and CompositeComponentTrait');
    });
});
