<?php

use ThenLabs\Components\Tests\Entity\Component;

setTestCaseNamespace('ThenLabs\Components\Tests');
setTestCaseClass('ThenLabs\Components\Tests\TestCase');

testCase('ComponentTest.php', function () {
    setUp(function () {
        $this->componentClass = Component::class;
    });

    testCase(sprintf("\$component = new \\%s;", Component::class), function () {
        useMacro('commons');
    });
});
