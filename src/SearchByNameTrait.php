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
        return $this->findChilds(function (ComponentInterface $component) use ($name) {
            if ($component instanceof ComponentWithNameInterface &&
                $component->getName() == $name
            ) {
                return $component;
            }
        });
    }
}
