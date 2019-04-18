<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
trait SearchByNameTrait
{
    public function findChildByName(string $name): ?ComponentInterface
    {
        if (! $this instanceof CompositeComponentInterface) {
            throw new \Exception('The SearchByNameTrait only can be used on a class that implements the CompositeComponentInterface.');
        }

        return $this->findChild(function (ComponentInterface $component) use ($name) {
            if ($component instanceof ComponentWithNameInterface &&
                $component->getName() == $name
            ) {
                return $component;
            }
        });
    }

    public function findChildsByName(string $name): array
    {
        if (! $this instanceof CompositeComponentInterface) {
            throw new \Exception('The SearchByNameTrait only can be used on a class that implements the CompositeComponentInterface.');
        }

        return $this->findChilds(function (ComponentInterface $component) use ($name) {
            if ($component instanceof ComponentWithNameInterface &&
                $component->getName() == $name
            ) {
                return $component;
            }
        });
    }
}
