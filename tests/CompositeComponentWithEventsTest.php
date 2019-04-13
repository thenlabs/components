<?php

use NubecuLabs\Components\Tests\Entity\Component;
use NubecuLabs\Components\Tests\Entity\CompositeComponentWithEvents;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('CompositeComponentWithEventsTest.php', function () {
    createMethod('getNewComponent', function () {
        return new CompositeComponentWithEvents;
    });

    testCase(sprintf("\$component = new \\%s;", CompositeComponentWithEvents::class), function () {
        useMacro('commons');

        useMacro('commons of ComponentWithEvents and CompositeComponentWithEvents');

        useMacro('commons of CompositeComponent and CompositeComponentWithEvents');

        testCase('$component->getId();', function () {
            test('returns an unique string that starts with "compositecomponentwithevents_"', function () {
                $id = $this->component->getId();

                $this->assertGreaterThan(13, strlen($id));
                $this->assertStringStartsWith('compositecomponentwithevents_', $id);
            });
        });

        testCase('$component->getCaptureEventDispatcher();', function () {
            test('returns an instance of "Symfony\Component\EventDispatcher\EventDispatcher"', function () {
                $this->assertInstanceOf(EventDispatcher::class, $this->component->getCaptureEventDispatcher());
            });

            test('returns always the same instance', function () {
                $dispatcher = $this->component->getCaptureEventDispatcher();

                $this->assertSame($dispatcher, $this->component->getCaptureEventDispatcher());
                $this->assertSame($dispatcher, $this->component->getCaptureEventDispatcher());
            });

            testCase('$component->setCaptureEventDispatcher($newDispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher);', function () {
                test('$component->getCaptureEventDispatcher() === $newDispatcher', function () {
                    $newDispatcher = new EventDispatcher;

                    $this->component->setCaptureEventDispatcher($newDispatcher);

                    $this->assertSame($newDispatcher, $this->component->getCaptureEventDispatcher());
                });
            });
        });
    });
});
