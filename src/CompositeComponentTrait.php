<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

use NubecuLabs\Components\Event\TreeEvent;
use NubecuLabs\Components\Event\AfterInsertionTreeEvent;
use NubecuLabs\Components\Event\AfterDeletionTreeEvent;
use NubecuLabs\Components\Event\BeforeInsertionTreeEvent;
use NubecuLabs\Components\Event\BeforeDeletionTreeEvent;
use NubecuLabs\Components\Event\BeforeOrderTreeEvent;
use NubecuLabs\Components\Event\FilterDependenciesEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
trait CompositeComponentTrait
{
    use ComponentTrait;

    /**
     * @var ComponentInterface[]
     */
    protected $childs = [];

    /**
     * @var EventDispatcherInterface
     */
    protected $captureEventDispatcher;

    /**
     * @see CompositeComponentInterface::addChilds()
     */
    public function addChilds(ComponentInterface ...$childs): void
    {
        foreach ($childs as $child) {
            $this->addChild($child);
        }
    }

    /**
     * @see CompositeComponentInterface::hasChild()
     */
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

    /**
     * @see CompositeComponentInterface::addChild()
     */
    public function addChild(ComponentInterface $child, $setParentInChild = true, bool $dispatchEvents = true): void
    {
        if (! $this->validateChild($child)) {
            throw new Exception\InvalidChildException(
                "Invalid child with id equal to '{$child->getId()}'."
            );
        }

        if ($dispatchEvents) {
            $beforeInsertionEvent = new BeforeInsertionTreeEvent($child, $this);
            $this->dispatchEvent(BeforeInsertionTreeEvent::class, $beforeInsertionEvent);

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
            $this->dispatchEvent(AfterInsertionTreeEvent::class, $afterInsertionEvent);
        }
    }

    /**
     * @see CompositeComponentInterface::getChild()
     */
    public function getChild(string $id): ?ComponentInterface
    {
        return $this->childs[$id] ?? null;
    }

    /**
     * @see CompositeComponentInterface::dropChild()
     */
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
                $this->dispatchEvent(BeforeDeletionTreeEvent::class, $beforeDeletionEvent);

                if ($beforeDeletionEvent->isCancelled()) {
                    return;
                }
            }

            unset($this->childs[$obj->getId()]);
            $obj->setParent(null, false, false);

            if ($dispatchEvents) {
                $afterDeletionEvent = new AfterDeletionTreeEvent($obj, $this);
                $this->dispatchEvent(AfterDeletionTreeEvent::class, $afterDeletionEvent);
            }
        }
    }

    /**
     * @see CompositeComponentInterface::getChilds()
     */
    public function getChilds(): array
    {
        return $this->childs;
    }

    /**
     * @see CompositeComponentInterface::children()
     */
    public function children(bool $deep = true): iterable
    {
        $generator = function (array $children) use (&$generator, $deep) {
            foreach ($children as $child) {
                yield $child;

                if ($deep && $child instanceof CompositeComponentInterface) {
                    yield from $generator($child->getChilds());
                }
            }

            return;
        };

        return $generator($this->childs);
    }

    /**
     * @see CompositeComponentInterface::findChild()
     */
    public function findChild(callable $callback, bool $deep = true): ?ComponentInterface
    {
        foreach ($this->children($deep) as $child) {
            if ($callback($child)) {
                return $child;
            }
        }

        return null;
    }

    /**
     * @see CompositeComponentInterface::findChilds()
     */
    public function findChilds(callable $callback, bool $deep = true): array
    {
        $childs = [];

        foreach ($this->children($deep) as $child) {
            if ($callback($child)) {
                $childs[] = $child;
            }
        }

        return $childs;
    }

    /**
     * @see CompositeComponentInterface::findChildById()
     */
    public function findChildById(string $id): ?ComponentInterface
    {
        return $this->findChild(function (ComponentInterface $child) use ($id) {
            if ($id == $child->getId()) {
                return $child;
            }
        });
    }

    /**
     * @see CompositeComponentInterface::findChildByName()
     */
    public function findChildByName(string $name): ?ComponentInterface
    {
        return $this->findChild(function (ComponentInterface $component) use ($name) {
            if ($component->getName() == $name) {
                return $component;
            }
        });
    }

    /**
     * @see CompositeComponentInterface::findChildsByName()
     */
    public function findChildsByName(string $name): array
    {
        return $this->findChilds(function (ComponentInterface $component) use ($name) {
            if ($component->getName() == $name) {
                return $component;
            }
        });
    }

    /**
     * @see CompositeComponentInterface::getCaptureEventDispatcher()
     */
    public function getCaptureEventDispatcher(): EventDispatcherInterface
    {
        if (! $this->captureEventDispatcher) {
            $this->captureEventDispatcher = new EventDispatcher;
        }

        return $this->captureEventDispatcher;
    }

    /**
     * @see CompositeComponentInterface::setCaptureEventDispatcher()
     */
    public function setCaptureEventDispatcher(EventDispatcherInterface $captureEventDispatcher): void
    {
        $this->captureEventDispatcher = $captureEventDispatcher;
    }

    /**
     * @see CompositeComponentInterface::on()
     */
    public function on(string $eventName, callable $listener, bool $capture = false): void
    {
        if ($capture) {
            $this->getCaptureEventDispatcher()->addListener($eventName, $listener);
        } else {
            $this->getEventDispatcher()->addListener($eventName, $listener);
        }
    }

    /**
     * @see CompositeComponentInterface::off()
     */
    public function off(string $eventName, callable $listener, bool $capture = false): void
    {
        if ($capture) {
            $this->getCaptureEventDispatcher()->removeListener($eventName, $listener);
        } else {
            $this->getEventDispatcher()->removeListener($eventName, $listener);
        }
    }

    /**
     * @see CompositeComponentInterface::validateChild()
     */
    public function validateChild(ComponentInterface $child): bool
    {
        return true;
    }

    /**
     * @see CompositeComponentInterface::getDependencies()
     */
    public function getDependencies(): array
    {
        $dependenciesOfChilds = [];
        foreach ($this->getChilds() as $child) {
            $dependenciesOfChilds = array_merge(
                $dependenciesOfChilds,
                $child->getDependencies()
            );
        }

        $dependencies = Helper::sortDependencies(
            array_merge(
                $this->getOwnDependencies(),
                $this->getAdditionalDependencies(),
                $dependenciesOfChilds
            ),
            $this
        );

        $event = new FilterDependenciesEvent($this, $dependencies);
        $eventName = FilterDependenciesEvent::class . "_{$this->getId()}";
        $this->dispatchEvent($eventName, $event);

        return $event->getDependencies();
    }

    /**
     * @see CompositeComponentInterface::getOrder()
     */
    public function getChildrenOrder(): array
    {
        return array_keys($this->childs);
    }

    /**
     * @param string[] $order
     */
    public function setChildrenOrder(array $order): void
    {
        if (count(array_diff(array_keys($this->childs), $order))) {
            throw new Exception\InvalidOrderException;
        }

        $beforeEvent = new BeforeOrderTreeEvent;
        $beforeEvent->setSource($this);
        $beforeEvent->setOldOrder($this->getChildrenOrder());
        $beforeEvent->setNewOrder($order);

        $this->dispatchEvent(BeforeOrderTreeEvent::class, $beforeEvent);

        $newChildsArray = [];

        foreach ($order as $componentId) {
            $newChildsArray[$componentId] = $this->childs[$componentId];
        }

        $this->childs = $newChildsArray;
    }
}
