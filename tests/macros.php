<?php

createMacro('common tests', function () {
    setUp(function () {
        $this->component = $this->getNewComponent();
    });

    testCase('#getId()', function () {
        test(function () {
            $this->assertNotEmpty($this->component->getId());
        });

        test(function () {
            $this->assertEquals($this->component->getId(), $this->component->getId());
        });
    });
});
