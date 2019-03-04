<?php

use NubecuLabs\Components\Tests\Component;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('Component tests', function () {
    createMethod('getNewComponent', function () {
        return new Component;
    });

    testCase('create a new instance of ' . Component::class, function () {
        useMacro('common tests for ComponentTrait and CompositeComponentTrait');

        testCase('related tests with the unique identifier', function () {
            test('the id is an unique string that starts with "component_"', function () {
                $id = $this->component->getId();

                $this->assertGreaterThan(13, strlen($id));
                $this->assertStringStartsWith('component_', $id);
            });
        });
    });
});
