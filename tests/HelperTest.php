<?php

use NubecuLabs\Components\Helper;
use NubecuLabs\Components\DependencyInterface;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('HelperTest.php', function () {
    test('testing Helper::setInstance() and Helper::getInstance() relationship', function () {
        $instance = $this->createMock(Helper::class);

        Helper::setInstance($instance);

        $this->assertSame($instance, Helper::getInstance());
    });

    test('the class "NubecuLabs\Components\Helper" is not instantiable', function () {
        $this->expectException(Error::class);

        new Helper;
    });

    testCase('#sortDependencies()', function () {
        test(function () {
            $dep1 = $this->createMock(DependencyInterface::class);
            $dep1->method('getName')->willReturn('dep1');

            $dep2 = $this->createMock(DependencyInterface::class);
            $dep2->method('getName')->willReturn('dep2');

            $deps = [$dep1, $dep2];
            $result = Helper::sortDependencies($deps);

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
            $result = Helper::sortDependencies($deps);

            $this->assertCount(3, $result);
            $this->assertEquals($dep1, $result['dep1']);
            $this->assertEquals($dep2, $result['dep2']);
            $this->assertEquals($dep3, $result['dep3']);
        });
    });
});
