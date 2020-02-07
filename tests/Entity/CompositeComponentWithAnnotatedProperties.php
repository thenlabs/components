<?php
declare(strict_types=1);

namespace ThenLabs\Components\Tests\Entity;

use ThenLabs\Components\CompositeComponentInterface;
use ThenLabs\Components\CompositeComponentTrait;
use ThenLabs\Components\AdditionalDependenciesFromAnnotationsTrait;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class CompositeComponentWithAnnotatedProperties implements CompositeComponentInterface
{
    use AnnotatedPropertiesTrait, CompositeComponentTrait, AdditionalDependenciesFromAnnotationsTrait {
        AdditionalDependenciesFromAnnotationsTrait::getAdditionalDependencies insteadof CompositeComponentTrait;
    }
}
