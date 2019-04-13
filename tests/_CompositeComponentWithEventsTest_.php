<?php

use NubecuLabs\Components\Tests\Entity\CompositeComponentWithEvents;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('CompositeComponentWithEventsTest.php', function () {
    createMethod('getNewComponent', function () {
        return new CompositeComponentWithEvents;
    });

    createMethod('getNewParentComponent', function () {
        return new CompositeComponentWithEvents;
    });

    testCase(sprintf("\$component = new \\%s;", CompositeComponentWithEvents::class), function () {
        testCase('$component->getId();', function () {
            test('returns an unique string that starts with "componentwithevents_"', function () {
                $id = $this->component->getId();

                $this->assertGreaterThan(13, strlen($id));
                $this->assertStringStartsWith('compositecomponentwithevents_', $id);
            });
        });

        useMacro('common tests');
    });
});
