<?php
declare(strict_types=1);

namespace ThenLabs\Components\Tests\Entity;

use ThenLabs\Components\AdditionalDependenciesFromAnnotationsTrait;
use ThenLabs\Components\CompositeComponentInterface;
use ThenLabs\Components\CompositeComponentTrait;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class CompositeComponentWithAnnotatedProperties implements CompositeComponentInterface
{
    use AnnotatedPropertiesTrait, CompositeComponentTrait, AdditionalDependenciesFromAnnotationsTrait {
        AdditionalDependenciesFromAnnotationsTrait::getAdditionalDependencies insteadof CompositeComponentTrait;
    }
}
