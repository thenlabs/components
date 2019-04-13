<?php

use NubecuLabs\Components\Tests\Entity\ComponentWithEvents;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('ComponentWithEventsTest.php', function () {
    setUp(function () {
        $this->componentClass = ComponentWithEvents::class;
    });

    testCase(sprintf("\$component = new \\%s;", ComponentWithEvents::class), function () {
        useMacro('commons');

        useMacro('commons of ComponentWithEvents and CompositeComponentWithEvents');

        testCase('$component->getId();', function () {
            test('returns an unique string that starts with "componentwithevents_"', function () {
                $id = $this->component->getId();

                $this->assertGreaterThan(13, strlen($id));
                $this->assertStringStartsWith('componentwithevents_', $id);
            });
        });
    });
});
