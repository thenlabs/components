<?php

use NubecuLabs\Components\Helper;
use NubecuLabs\Components\Event\Event;
use NubecuLabs\Components\Event\DependencyConflictEvent;
use NubecuLabs\Components\DependencyInterface;
use NubecuLabs\Components\Exception\InvalidConflictDispatcherException;
use NubecuLabs\Components\Exception\UnresolvedDependencyConflictException;
use NubecuLabs\Components\Tests\Entity\Component;
use Symfony\Component\EventDispatcher\EventDispatcher;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('HelperTest.php', function () {
    testCase('#sortDependencies()', function () {
        test(function () {
            $this->expectException(InvalidConflictDispatcherException::class);
            $this->expectExceptionMessage('The conflict dispatcher may be only an instance of "Symfony\Component\EventDispatcher\EventDispatcherInterface" or "NubecuLabs\Components\ComponentInterface".');

            Helper::sortDependencies([], uniqid());
        });

        test(function () {
            $dep1 = $this->createMock(DependencyInterface::class);
            $dep1->method('getName')->willReturn('dep1');

            $dep2 = $this->createMock(DependencyInterface::class);
            $dep2->method('getName')->willReturn('dep2');

            $deps = [$dep1, $dep2, 1, uniqid(), range(1, 10)];
            $result = Helper::sortDependencies($deps, new EventDispatcher);

            $this->assertCount(2, $result);
            $this->assertEquals($dep1, $result['dep1']);
            $this->assertEquals($dep2, $result['dep2']);
        });

        test(function () {
            $dep1 = $this->createMock(DependencyInterface::class);
            $dep1->method('getName')->willReturn('dep1');

            $dep2 = $this->createMock(DependencyInterface::class);
            $dep2->method('getName')->willReturn('dep2');
            $dep2->method('getDependencies')->willReturn([$dep1]);

            $dep3 = $this->createMock(DependencyInterface::class);
            $dep3->method('getName')->willReturn('dep3');
            $dep3->method('getDependencies')->willReturn([$dep2]);

            $deps = [$dep3];
            $result = Helper::sortDependencies($deps, new EventDispatcher);

            $this->assertCount(3, $result);
            $this->assertEquals($dep1, $result['dep1']);
            $this->assertEquals($dep2, $result['dep2']);
            $this->assertEquals($dep3, $result['dep3']);
        });

        test(function () {
            $dep1 = $this->createMock(DependencyInterface::class);
            $dep1->method('getName')->willReturn('dep1');

            $dep2 = $this->createMock(DependencyInterface::class);
            $dep2->method('getName')->willReturn('dep2');
            $dep2->method('getIncludedDependencies')->willReturn([$dep1]);

            $deps = [$dep1, $dep2];
            $result = Helper::sortDependencies($deps, new EventDispatcher);

            $this->assertCount(1, $result);
            $this->assertEquals($dep2, $result['dep2']);
        });

        test(function () {
            $dep1 = $this->createMock(DependencyInterface::class);
            $dep1->method('getName')->willReturn('dep1');

            $dep2 = $this->createMock(DependencyInterface::class);
            $dep2->method('getName')->willReturn('dep2');
            $dep2->method('getIncludedDependencies')->willReturn([$dep1]);

            $deps = [$dep2, $dep1];
            $result = Helper::sortDependencies($deps, new EventDispatcher);

            $this->assertCount(1, $result);
            $this->assertEquals($dep2, $result['dep2']);
        });

        test(function () {
            $dep1 = $this->createMock(DependencyInterface::class);
            $dep1->method('getName')->willReturn('dep1');

            $deps = [$dep1, $dep1, $dep1];
            $result = Helper::sortDependencies($deps, new EventDispatcher);

            $this->assertCount(1, $result);
            $this->assertEquals($dep1, $result['dep1']);
        });

        testCase('exists two dependency with equal name in the same level', function () {
            setUp(function () {
                $this->name = uniqid('dependency') ;

                $this->dep1 = $this->createMock(DependencyInterface::class);
                $this->dep1->method('getName')->willReturn($this->name);

                $this->dep2 = $this->createMock(DependencyInterface::class);
                $this->dep2->method('getName')->willReturn($this->name);

                $this->deps = [$this->dep1, $this->dep2];
            });

            test('it is triggered an UnresolvedDependencyConflictException', function () {
                $this->expectException(UnresolvedDependencyConflictException::class);
                $this->expectExceptionMessage("Conflict between dependencies with name '{$this->name}'.");

                $result = Helper::sortDependencies($this->deps, new EventDispatcher);
            });

            test(function () {
                $eventName = Event::DEPENDENCY_CONFLICT . $this->name;

                $dispatcher = new EventDispatcher;
                $dispatcher->addListener($eventName, function (DependencyConflictEvent $event) {
                    $this->executed = true;

                    $this->assertSame($this->dep1, $event->getDependency1());
                    $this->assertSame($this->dep2, $event->getDependency2());

                    $event->setSolution($this->dep1);
                });

                $result = Helper::sortDependencies($this->deps, $dispatcher);

                $this->assertCount(1, $result);
                $this->assertTrue($this->executed);
                $this->assertSame($this->dep1, $result[$this->name]);
            });

            test(function () {
                $eventName = Event::DEPENDENCY_CONFLICT . $this->name;

                $component = new Component;
                $component->on($eventName, function (DependencyConflictEvent $event) {
                    $this->executed = true;

                    $this->assertSame($this->dep1, $event->getDependency1());
                    $this->assertSame($this->dep2, $event->getDependency2());

                    $event->setSolution($this->dep1);
                });

                $result = Helper::sortDependencies($this->deps, $component);

                $this->assertCount(1, $result);
                $this->assertTrue($this->executed);
                $this->assertSame($this->dep1, $result[$this->name]);
            });

            testCase('automatic conflict solutions when both has information about his versions', function () {
                setUp(function () {
                    $minorValue = mt_rand(1, 5);
                    $majorValue = $minorValue + 1;

                    $this->minor = "1.{$minorValue}.0";
                    $this->major = "1.{$majorValue}.0";
                });

                test(function () {
                    $this->dep1->method('getVersion')->willReturn($this->major);
                    $this->dep2->method('getVersion')->willReturn($this->minor);

                    $dispatcher = new EventDispatcher;
                    $result = Helper::sortDependencies($this->deps, $dispatcher);

                    $this->assertCount(1, $result);
                    $this->assertSame($this->dep1, $result[$this->name]);
                });

                test(function () {
                    $this->dep1->method('getVersion')->willReturn($this->minor);
                    $this->dep2->method('getVersion')->willReturn($this->major);

                    $dispatcher = new EventDispatcher;
                    $result = Helper::sortDependencies($this->deps, $dispatcher);

                    $this->assertCount(1, $result);
                    $this->assertSame($this->dep2, $result[$this->name]);
                });

                test(function () {
                    $this->dep1->method('getVersion')->willReturn($this->minor);
                    $this->dep2->method('getVersion')->willReturn($this->minor);

                    $dispatcher = new EventDispatcher;
                    $result = Helper::sortDependencies($this->deps, $dispatcher);

                    $this->assertCount(1, $result);
                    $this->assertSame($this->dep1, $result[$this->name]);
                });
            });
        });
    });
});
