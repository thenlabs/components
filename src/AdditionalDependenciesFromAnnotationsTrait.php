<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

use Doctrine\Common\Annotations\AnnotationReader;
use NubecuLabs\Components\Annotation\Component as ComponentAnnotation;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
trait AdditionalDependenciesFromAnnotationsTrait
{
    public function getAdditionalDependencies(): array
    {
        $result = [];
        $reader = new AnnotationReader();

        // Hack for load the annotation class. If is omitted it's throws a doctrine exception.
        new ComponentAnnotation;

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
