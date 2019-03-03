<?php

use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\CompositeComponentInterface;

createMacro('common tests for ComponentTrait and CompositeComponentTrait', function () {
    setUp(function () {
        $this->component = $this->getNewComponent();
    });

    testCase('#getId()', function () {
        test(function () {
            $this->assertEquals($this->component->getId(), $this->component->getId());
        });
    });

    testCase('related tests with the parent', function () {
        test('by default has not parent', function () {
            $this->assertNull($this->component->getParent());
        });

        test('tests relationship between #getParent() and #setParent()', function () {
            $parent = $this->createMock(CompositeComponentInterface::class);

            $this->component->setParent($parent);

            $this->assertEquals($parent, $this->component->getParent());
        });
    });
});
