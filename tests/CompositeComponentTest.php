<?php

use NubecuLabs\Components\Tests\CompositeComponent;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('CompositeComponent tests', function () {
    createMethod('getNewComponent', function () {
        return new CompositeComponent;
    });

    useMacro('common tests');

    test('the id is an unique string that starts with "compositecomponent_"', function () {
        $id = $this->component->getId();

        $this->assertGreaterThan(13, strlen($id));
        $this->assertStringStartsWith('compositecomponent_', $id);
    });
});
