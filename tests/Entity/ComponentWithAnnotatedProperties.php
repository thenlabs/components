<?php
declare(strict_types=1);

namespace ThenLabs\Components\Tests\Entity;

use ThenLabs\Components\ComponentInterface;
use ThenLabs\Components\ComponentTrait;
use ThenLabs\Components\AdditionalDependenciesFromAnnotationsTrait;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class ComponentWithAnnotatedProperties implements ComponentInterface
{
    use AnnotatedPropertiesTrait, ComponentTrait, AdditionalDependenciesFromAnnotationsTrait {
        AdditionalDependenciesFromAnnotationsTrait::getAdditionalDependencies insteadof ComponentTrait;
    }
}
