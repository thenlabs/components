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

    public function addChild(ComponentInterface $child, $setParentInChild = true): void
    {
        $this->children[$child->getId()] = $child;

        if ($setParentInChild) {
            $child->setParent($this);
        }
    }

    public function dropChild($child): void
    {
        $obj = null;

        if (is_string($child)) {
            $obj = $this->children[$child] ?? null;
        }

        if ($child instanceof ComponentInterface) {
            $obj = $child;
        }

        if ($obj instanceof ComponentInterface &&
            isset($this->children[$obj->getId()])
        ) {
            unset($this->children[$obj->getId()]);
            $obj->setParent(null, false);
        }
    }

    public function getChildren(): array
    {
        return $this->children;
    }
}
