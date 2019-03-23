<?php

use NubecuLabs\Components\Tests\Component;
use NubecuLabs\Components\Tests\CompositeComponent;
use NubecuLabs\Components\Event\TreeEvent;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('TreeEventTest.php', function () {
    testCase('$event = new Event\TreeEvent($child = new Component, $parent = new CompositeComponent);', function () {
        setUp(function () {
            $this->child = new Component;
            $this->parent = new CompositeComponent;
            $this->event = new TreeEvent($this->child, $this->parent);
        });

        test('$event->getChild() === $child', function () {
            $this->assertSame($this->child, $this->event->getChild());
        });

        test('$event->getParent() === $parent', function () {
            $this->assertSame($this->parent, $this->event->getParent());
        });
    });
});
