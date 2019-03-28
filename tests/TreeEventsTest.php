<?php

use NubecuLabs\Components\Event\TreeEvent;
use NubecuLabs\Components\Event\BeforeInsertionTreeEvent;
use NubecuLabs\Components\Tests\Component;
use NubecuLabs\Components\Tests\CompositeComponent;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('TreeEventsTest.php', function () {
    setUp(function () {
        $this->executedListener = false;
        $this->child = new Component;
        $this->parent = new CompositeComponent;

        $this->beforeInsertionListener1 = function (BeforeInsertionTreeEvent $event) {
            $this->executedListener = true;
            $this->assertSame($this->parent, $event->getParent());
            $this->assertSame($this->child, $event->getChild());
            $this->assertFalse($event->isCancelled());
        };

        // listener2 cancel the insertion
        $this->beforeInsertionListener2 = function (BeforeInsertionTreeEvent $event) {
            $this->executedListener = true;
            $this->assertSame($this->parent, $event->getParent());
            $this->assertSame($this->child, $event->getChild());

            $event->cancel();
            $this->assertTrue($event->isCancelled());
        };
    });

    testCase('testing before insertion cases', function () {
        setUp(function () {
            $this->parent->on(TreeEvent::BEFORE_INSERTION, $this->beforeInsertionListener1);
        });

        test(function () {
            $this->parent->addChild($this->child); // Act

            $this->assertTrue($this->executedListener);
            $this->assertTrue($this->parent->hasChild($this->child));
        });
    });
});
