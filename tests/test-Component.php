<?php

use ThenLabs\Components\Tests\Entity\Component;

setTestCaseClass('ThenLabs\Components\Tests\TestCase');

testCase('test-Component.php', function () {
    setUp(function () {
        $this->componentClass = Component::class;
    });

    testCase(sprintf("\$component = new \\%s;", Component::class), function () {
        useMacro('commons');
    });
});
