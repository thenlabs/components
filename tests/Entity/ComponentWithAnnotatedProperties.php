<?php
declare(strict_types=1);

namespace ThenLabs\Components\Tests\Entity;

use ThenLabs\Components\AdditionalDependenciesFromAnnotationsTrait;
use ThenLabs\Components\ComponentInterface;
use ThenLabs\Components\ComponentTrait;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class ComponentWithAnnotatedProperties implements ComponentInterface
{
    use AnnotatedPropertiesTrait, ComponentTrait, AdditionalDependenciesFromAnnotationsTrait {
        AdditionalDependenciesFromAnnotationsTrait::getAdditionalDependencies insteadof ComponentTrait;
    }
}
