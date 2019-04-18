<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
trait SearchByNameTrait
{
    private function _validateThisInstanceForSearchsByName(): void
    {
        if (! $this instanceof CompositeComponentInterface) {
            throw new \Exception('The SearchByNameTrait only can be used on a class that implements the CompositeComponentInterface.');
        }
    }

    public function findChildByName(string $name): ?ComponentInterface
    {
        $this->_validateThisInstanceForSearchsByName();

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
        $this->_validateThisInstanceForSearchsByName();

        return $this->findChilds(function (ComponentInterface $component) use ($name) {
            if ($component instanceof ComponentWithNameInterface &&
                $component->getName() == $name
            ) {
                return $component;
            }
        });
    }
}
