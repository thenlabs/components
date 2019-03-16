<?php

use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\CompositeComponentInterface;

createMacro('common tests for ComponentTrait and CompositeComponentTrait', function () {
    setUp(function () {
        $this->component = $this->getNewComponent();
    });

    testCase('$component->getId();', function () {
        test('always returns the same value', function () {
            $id = $this->component->getId();

            $this->assertEquals($id, $this->component->getId());
            $this->assertEquals($id, $this->component->getId());
            $this->assertEquals($id, $this->component->getId());
        });
    });

    testCase('$component->getParent();', function () {
        test('returns null', function () {
            $this->assertNull($this->component->getParent());
        });
    });
});
