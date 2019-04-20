<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Tests\Entity;

use NubecuLabs\Components\CompositeComponentInterface;
use NubecuLabs\Components\CompositeComponentTrait;
use NubecuLabs\Components\AdditionalDependenciesFromAnnotationsTrait;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class CompositeComponentWithAnnotatedProperties implements CompositeComponentInterface
{
    use AnnotatedPropertiesTrait, CompositeComponentTrait, AdditionalDependenciesFromAnnotationsTrait {
        AdditionalDependenciesFromAnnotationsTrait::getAdditionalDependencies insteadof CompositeComponentTrait;
    }
}
