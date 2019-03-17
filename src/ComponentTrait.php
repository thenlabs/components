<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
trait ComponentTrait
{
    private $id;

    protected $parent;

    public function getId(): string
    {
        if (! $this->id) {
            $nameParts = explode('\\', __CLASS__);
            $shortLowerName = strtolower(array_pop($nameParts));

            $this->id = uniqid($shortLowerName . '_');
        }

        return $this->id;
    }

    public function getParent(): ?CompositeComponentInterface
    {
        return $this->parent;
    }

    public function setParent(?CompositeComponentInterface $parent, bool $addChild = true): void
    {
        if ($this->parent instanceof CompositeComponentInterface) {
            $this->parent->dropChild($this);
        }

        $this->parent = $parent;

        if ($parent && $addChild) {
            $this->parent->addChild($this);
        }
    }

    public function getDependencies(): array
    {
        return [];
    }
}
