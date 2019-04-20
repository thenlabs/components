<?php

use NubecuLabs\Components\Helper;
use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\CompositeComponentInterface;
use NubecuLabs\Components\ComponentTrait;
use NubecuLabs\Components\CompositeComponentTrait;
use NubecuLabs\Components\AdditionalDependenciesFromAnnotationsTrait;
use NubecuLabs\Components\Annotation\Component;
use NubecuLabs\Components\Tests\Entity\ComponentWithAnnotatedProperties;

setTestCaseNamespace('NubecuLabs\Components\Tests');
setTestCaseClass('NubecuLabs\Components\Tests\TestCase');

testCase('AdditionalDependenciesFromAnnotationsTraitTest.php', function () {
    testCase('when component is only ComponentInterface', function () {
        test(function () {
            $dependencies1 = $this->getRandomArray('c1_');
            $dependencies2 = $this->getRandomArray('c2_');
            $dependencies3 = $this->getRandomArray('c3_');

            $component1 = $this->createMock(ComponentInterface::class);
            $component1->method('getDependencies')->willReturn($dependencies1);

            $component2 = $this->createMock(ComponentInterface::class);
            $component2->method('getDependencies')->willReturn($dependencies2);

            $component3 = $this->createMock(ComponentInterface::class);
            $component3->method('getDependencies')->willReturn($dependencies3);

            $component = new ComponentWithAnnotatedProperties;
            $component->setProperty1($component1);
            $component->setProperty2($component2);
            $component->property3 = $component3;

            $this->assertEquals(
                array_merge($dependencies1, $dependencies2, $dependencies3),
                $component->getAdditionalDependencies()
            );
        });
    });
});
