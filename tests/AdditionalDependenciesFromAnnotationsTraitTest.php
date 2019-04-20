<?php

use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\Tests\Entity\ComponentWithAnnotatedProperties;
use NubecuLabs\Components\Tests\Entity\CompositeComponentWithAnnotatedProperties;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('AdditionalDependenciesFromAnnotationsTraitTest.php', function () {
    setUp(function () {
        $this->dependencies1 = $this->getRandomArray('c1_');
        $this->dependencies2 = $this->getRandomArray('c2_');
        $this->dependencies3 = $this->getRandomArray('c3_');

        $this->component1 = $this->createMock(ComponentInterface::class);
        $this->component1->method('getDependencies')->willReturn($this->dependencies1);

        $this->component2 = $this->createMock(ComponentInterface::class);
        $this->component2->method('getDependencies')->willReturn($this->dependencies2);

        $this->component3 = $this->createMock(ComponentInterface::class);
        $this->component3->method('getDependencies')->willReturn($this->dependencies3);
    });

    createMethod('assignPropertiesAndDoAsserts', function () {
        $this->component->setProperty1($this->component1);
        $this->component->setProperty2($this->component2);
        $this->component->property3 = $this->component3;

        $this->assertEquals(
            array_merge($this->dependencies1, $this->dependencies2, $this->dependencies3),
            $this->component->getAdditionalDependencies()
        );
    });

    test('when component is only instance of ComponentInterface', function () {
        $this->component = new ComponentWithAnnotatedProperties;

        $this->assignPropertiesAndDoAsserts();
    });

    test('when component is instance of CompositeComponentInterface', function () {
        $this->component = new CompositeComponentWithAnnotatedProperties;

        $this->assignPropertiesAndDoAsserts();
    });
});
