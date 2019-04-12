<?php

use NubecuLabs\Components\Tests\Entity\Component;
use NubecuLabs\Components\Tests\Entity\CompositeComponent;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('ComponentTest.php', function () {
    createMethod('getNewComponent', function () {
        return new Component;
    });

    createMethod('getNewParentComponent', function () {
        return new CompositeComponent;
    });

    testCase(sprintf("\$component = new \\%s;", Component::class), function () {
        testCase('$component->getId();', function () {
            test('returns an unique string that starts with "component_"', function () {
                $id = $this->component->getId();

                $this->assertGreaterThan(13, strlen($id));
                $this->assertStringStartsWith('component_', $id);
            });
        });

        useMacro('common tests');
    });
});
