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
    use ComponentTrait { getDependencies as getOwnDependencies; }

    protected $childs = [];

    protected $captureEventDispatcher;

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

    public function getChilds(): array
    {
        return $this->childs;
    }

    public function children(bool $recursive = true): iterable
    {
        $generator = function (array $children) use (&$generator, $recursive) {
            foreach ($children as $child) {
                yield $child;

                if ($recursive && $child instanceof CompositeComponentInterface) {
                    yield from $generator($child->getChilds());
                }
            }

            return;
        };

        return $generator($this->childs);
    }

    public function findChild(callable $callback, bool $recursive = true): ?ComponentInterface
    {
        foreach ($this->children($recursive) as $child) {
            if ($callback($child)) {
                return $child;
            }
        }

        return null;
    }

    public function findChilds(callable $callback, bool $recursive = true): array
    {
        $childs = [];

        foreach ($this->children($recursive) as $child) {
            if ($callback($child)) {
                $childs[] = $child;
            }
        }

        return $childs;
    }

    public function findChildById(string $id): ?ComponentInterface
    {
        return $this->findChild(function (ComponentInterface $child) use ($id) {
            return $id == $child->getId() ? true : false;
        });
    }

    public function getCaptureEventDispatcher(): EventDispatcherInterface
    {
        if (! $this->captureEventDispatcher) {
            $this->captureEventDispatcher = new EventDispatcher;
        }

        return $this->captureEventDispatcher;
    }

    public function setCaptureEventDispatcher(EventDispatcherInterface $captureEventDispatcher): void
    {
        $this->captureEventDispatcher = $captureEventDispatcher;
    }

    public function on(string $eventName, callable $listener, bool $capture = false): void
    {
        $this->getEventDispatcher()->addListener($eventName, $listener);
    }

    public function off(string $eventName, callable $listener, bool $capture = false): void
    {
        $this->getEventDispatcher()->removeListener($eventName, $listener);
    }
}
