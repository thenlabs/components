<?php

use NubecuLabs\Components\ComponentTrait;
use NubecuLabs\Components\Tests\Component;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('ComponentTraitTest.php', function () {
    test('#getDependencies() returns an empty array by default', function () {
        $trait = $this->getMockForTrait(ComponentTrait::class);

        $this->assertEquals([], $trait->getDependencies());
    });

    test('#getDependencies() returns the #dependencies data', function () {
        $expectedData = range(1, mt_rand(1, 10)); // random array
        $component = new Component;
        $component->setDependencies($expectedData);

        $this->assertEquals($expectedData, $component->getDependencies());
    });

    test('#detach() invoke to #setParent(null)', function () {
        $mock = $this->getMockBuilder(ComponentTrait::class)
            ->setMethods(['setParent'])
            ->getMockForTrait();
        $mock->expects($this->once())
            ->method('setParent')
            ->with($this->equalTo(false))
        ;

        $mock->detach();
    });

    test('#on($eventName, $listener) invoke to #eventDispatcher->addListener($eventName, $listener)', function () {
        $eventName = uniqid('eventName');
        $listener = function () {
        };

        $dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
            ->setMethods(['addListener'])
            ->getMockForAbstractClass();
        $dispatcher->expects($this->once())
            ->method('addListener')
            ->with(
                $this->equalTo($eventName),
                $this->equalTo($listener)
            )
        ;

        $trait = $this->getMockForTrait(ComponentTrait::class);
        $trait->setEventDispatcher($dispatcher);

        $trait->on($eventName, $listener); // Act
    });

    test('#off($eventName, $listener) invoke to #eventDispatcher->removeListener($eventName, $listener)', function () {
        $eventName = uniqid('eventName');
        $listener = function () {
        };

        $dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
            ->setMethods(['removeListener'])
            ->getMockForAbstractClass();
        $dispatcher->expects($this->once())
            ->method('removeListener')
            ->with(
                $this->equalTo($eventName),
                $this->equalTo($listener)
            )
        ;

        $trait = $this->getMockForTrait(ComponentTrait::class);
        $trait->setEventDispatcher($dispatcher);

        $trait->off($eventName, $listener); // Act
    });
});
