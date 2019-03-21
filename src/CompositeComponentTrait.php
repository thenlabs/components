<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
trait CompositeComponentTrait
{
    use ComponentTrait;

    protected $childs = [];

    protected $eventDispatcher;

    public function hasChild($child): bool
    {
        if (is_string($child) && isset($this->childs[$child])) {
            return true;
        }

        if ($child instanceof ComponentInterface &&
            isset($this->childs[$child->getId()])
        ) {
            return true;
        }

        return false;
    }

    public function addChild(ComponentInterface $child, $setParentInChild = true): void
    {
        $this->childs[$child->getId()] = $child;

        if ($setParentInChild) {
            $child->setParent($this);
        }
    }

    public function getChild(string $id): ?ComponentInterface
    {
        return $this->childs[$id] ?? null;
    }

    public function dropChild($child): void
    {
        $obj = null;

        if (is_string($child)) {
            $obj = $this->childs[$child] ?? null;
        }

        if ($child instanceof ComponentInterface) {
            $obj = $child;
        }

        if ($obj instanceof ComponentInterface &&
            isset($this->childs[$obj->getId()])
        ) {
            unset($this->childs[$obj->getId()]);
            $obj->setParent(null, false);
        }
    }

    public function getOwnChilds(): array
    {
        return $this->childs;
    }

    public function children(): iterable
    {
        $generator = function (array $children) use (&$generator) {
            foreach ($children as $child) {
                yield $child;

                if ($child instanceof CompositeComponentInterface) {
                    yield from $generator($child->getOwnChilds());
                }
            }

            return;
        };

        return $generator($this->childs);
    }

    public function getEventDispatcher(): EventDispatcherInterface
    {
        if (! $this->eventDispatcher) {
            $this->eventDispatcher = new EventDispatcher;
        }

        return $this->eventDispatcher;
    }
}
