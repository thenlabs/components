<?php

use NubecuLabs\Components\Helper;

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
});
