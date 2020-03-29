<?php
declare(strict_types=1);

namespace ThenLabs\Components;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use ThenLabs\Components\Annotation\Component as ComponentAnnotation;

AnnotationRegistry::registerFile(__DIR__.'/Annotation/Component.php');

/**
 * Use this trait for get the additional dependencies of a component from attributes
 * that have the @Component annotation.
 *
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
trait AdditionalDependenciesFromAnnotationsTrait
{
    /**
     * @see     \ThenLabs\Components\ComponentInterface::getAdditionalDependencies()
     * @return  DependencyInterface[]
     */
    public function getAdditionalDependencies(): array
    {
        $result = [];
        $reader = new AnnotationReader();

        $class = new \ReflectionClass($this);
        foreach ($class->getProperties() as $property) {
            foreach ($reader->getPropertyAnnotations($property) as $annotation) {
                if ($annotation instanceof ComponentAnnotation) {
                    $property->setAccessible(true);
                    $component = $property->getValue($this);
                    if ($component instanceof ComponentInterface) {
                        $result = array_merge($result, $component->getDependencies());
                    }
                }
            }
        }

        return $result;
    }
}
