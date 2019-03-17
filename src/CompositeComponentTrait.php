<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
trait CompositeComponentTrait
{
    use ComponentTrait;

    protected $children = [];

    public function hasChild($child): bool
    {
        if (is_string($child) && isset($this->children[$child])) {
            return true;
        }

        if ($child instanceof ComponentInterface &&
            isset($this->children[$child->getId()])
        ) {
            return true;
        }

        return false;
    }

    public function addChild(ComponentInterface $child): void
    {
        $this->children[$child->getId()] = $child;
    }

    public function dropChild($child): void
    {
        $this->children = [];
    }
}
