<?php

use NubecuLabs\Components\Tests\Entity\ComponentWithEvents;
use NubecuLabs\Components\Tests\Entity\CompositeComponentWithEvents;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('ComponentWithEventsTest.php', function () {
    createMethod('getNewComponent', function () {
        return new ComponentWithEvents;
    });

    createMethod('getNewParentComponent', function () {
        return new CompositeComponentWithEvents;
    });

    testCase(sprintf("\$component = new \\%s;", ComponentWithEvents::class), function () {
        testCase('$component->getId();', function () {
            test('returns an unique string that starts with "componentwithevents_"', function () {
                $id = $this->component->getId();

                $this->assertGreaterThan(13, strlen($id));
                $this->assertStringStartsWith('componentwithevents_', $id);
            });
        });

        useMacro('common tests');
    });
});
