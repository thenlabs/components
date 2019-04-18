<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

use NubecuLabs\Components\Exception\SearchsByNameTraitException;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
trait SearchByNameTrait
{
    public function findChildByName(string $name): ?ComponentInterface
    {
        if (! $this instanceof CompositeComponentInterface) {
            throw new SearchsByNameTraitException;
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
            throw new SearchsByNameTraitException;
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
