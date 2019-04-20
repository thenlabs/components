<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Tests\Entity;

use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\ComponentTrait;
use NubecuLabs\Components\AdditionalDependenciesFromAnnotationsTrait;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class ComponentWithAnnotatedProperties implements ComponentInterface
{
    use AnnotatedPropertiesTrait, ComponentTrait, AdditionalDependenciesFromAnnotationsTrait {
        AdditionalDependenciesFromAnnotationsTrait::getAdditionalDependencies insteadof ComponentTrait;
    }
}
