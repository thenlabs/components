<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

use NubecuLabs\Components\Event\TreeEvent;
use NubecuLabs\Components\Event\AfterInsertionTreeEvent;
use NubecuLabs\Components\Event\AfterDeletionTreeEvent;
use NubecuLabs\Components\Event\BeforeInsertionTreeEvent;
use NubecuLabs\Components\Event\BeforeDeletionTreeEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
trait CompositeComponentTrait
{
    use ComponentTrait;

    protected $childs = [];

    protected $captureEventDispatcher;

    public function addChilds(ComponentInterface ...$childs): void
    {
        foreach ($childs as $child) {
            $this->addChild($child);
        }
    }

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

    public function addChild(ComponentInterface $child, $setParentInChild = true, bool $dispatchEvents = true): void
    {
        if (! $this->validateChild($child)) {
            throw new Exception\InvalidChildException(
                "Invalid child with id equal to '{$child->getId()}'."
            );
        }

        if ($dispatchEvents) {
            $beforeInsertionEvent = new BeforeInsertionTreeEvent($child, $this);
            $this->dispatchEvent(TreeEvent::BEFORE_INSERTION, $beforeInsertionEvent);

            if ($beforeInsertionEvent->isCancelled()) {
                return;
            }
        }

        $this->childs[$child->getId()] = $child;

        if ($setParentInChild) {
            $child->setParent($this, false, false);
        }

        if ($dispatchEvents) {
            $afterInsertionEvent = new AfterInsertionTreeEvent($child, $this);
            $this->dispatchEvent(TreeEvent::AFTER_INSERTION, $afterInsertionEvent);
        }
    }

    public function getChild(string $id): ?ComponentInterface
    {
        return $this->childs[$id] ?? null;
    }

    public function dropChild($child, bool $dispatchEvents = true): void
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
            if ($dispatchEvents) {
                $beforeDeletionEvent = new BeforeDeletionTreeEvent($obj, $this);
                $this->dispatchEvent(TreeEvent::BEFORE_DELETION, $beforeDeletionEvent);

                if ($beforeDeletionEvent->isCancelled()) {
                    return;
                }
            }

            unset($this->childs[$obj->getId()]);
            $obj->setParent(null, false, false);

            if ($dispatchEvents) {
                $afterDeletionEvent = new AfterDeletionTreeEvent($obj, $this);
                $this->dispatchEvent(TreeEvent::AFTER_DELETION, $afterDeletionEvent);
            }
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
            if ($id == $child->getId()) {
                return $child;
            }
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
        if ($capture) {
            $this->getCaptureEventDispatcher()->addListener($eventName, $listener);
        } else {
            $this->getEventDispatcher()->addListener($eventName, $listener);
        }
    }

    public function off(string $eventName, callable $listener, bool $capture = false): void
    {
        if ($capture) {
            $this->getCaptureEventDispatcher()->removeListener($eventName, $listener);
        } else {
            $this->getEventDispatcher()->removeListener($eventName, $listener);
        }
    }

    public function validateChild(ComponentInterface $child): bool
    {
        return true;
    }

    public function getDependencies(array $options = []): array
    {
        $dependenciesOfChilds = [];
        foreach ($this->getChilds() as $child) {
            $dependenciesOfChilds = array_merge(
                $dependenciesOfChilds,
                $child->getDependencies($options)
            );
        }

        return Helper::sortDependencies(
            array_merge(
                $this->getOwnDependencies($options),
                $this->getAdditionalDependencies($options),
                $dependenciesOfChilds
            ),
            $this,
            $options
        );
    }
}
