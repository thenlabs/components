<?php

use NubecuLabs\Components\CompositeComponentTrait;
use NubecuLabs\Components\Tests\Component;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('CompositeComponentTraitTest.php', function () {
    test('#on($eventName, $listener) invoke to #eventDispatcher->addListener($eventName, $listener)', function () {
        $eventName = uniqid('eventName');
        $listener = function () {};

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

        $trait = $this->getMockForTrait(CompositeComponentTrait::class);
        $trait->setEventDispatcher($dispatcher);

        $trait->on($eventName, $listener); // Act
    });
});
