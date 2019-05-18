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
    });
});
