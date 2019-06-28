<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

use NubecuLabs\Components\Event\TreeEvent;
use NubecuLabs\Components\Event\AfterInsertionTreeEvent;
use NubecuLabs\Components\Event\AfterDeletionTreeEvent;
use NubecuLabs\Components\Event\BeforeInsertionTreeEvent;
use NubecuLabs\Components\Event\BeforeDeletionTreeEvent;
use NubecuLabs\Components\Event\FilterDependenciesEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
trait ComponentTrait
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var CompositeComponentInterface|null
     */
    protected $parent;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @see ComponentInterface::getId()
     */
    public function getId(): string
    {
        if (! $this->id) {
            $this->id = uniqid('comp_');
        }

        return $this->id;
    }

    /**
     * @see ComponentInterface::getName()
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @see ComponentInterface::setName()
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @see ComponentInterface::getParent()
     */
    public function getParent(): ?CompositeComponentInterface
    {
        return $this->parent;
    }

    /**
     * @see ComponentInterface::getParents()
     */
    public function getParents(): array
    {
        $parents = [];

        foreach ($this->parents() as $parent) {
            $parents[] = $parent;
        }

        return $parents;
    }

    /**
     * @see ComponentInterface::parents()
     */
    public function parents(): iterable
    {
        $node = $this;

        while ($node) {
            $parent = $node->getParent();

            if ($parent) {
                yield $parent;
            } else {
                return;
            }

            $node = $parent;
        }
    }

    /**
     * @see ComponentInterface::setParent()
     */
    public function setParent(?CompositeComponentInterface $parent, bool $addChildToParent = true, bool $dispatchEvents = true): void
    {
        if ($this->parent instanceof CompositeComponentInterface) {
            if ($dispatchEvents) {
                $beforeDeletionEvent = new BeforeDeletionTreeEvent($this, $this->parent);
                $this->parent->dispatchEvent(TreeEvent::BEFORE_DELETION, $beforeDeletionEvent);

                if ($beforeDeletionEvent->isCancelled()) {
                    return;
                }
            }

            $oldParent = $this->parent;
            $this->parent->dropChild($this, false, false);

            if ($oldParent instanceof CompositeComponentInterface && $dispatchEvents) {
                $afterDeletionEvent = new AfterDeletionTreeEvent($this, $oldParent);
                $oldParent->dispatchEvent(TreeEvent::AFTER_DELETION, $afterDeletionEvent);
            }
        }

        if ($parent instanceof CompositeComponentInterface && $dispatchEvents) {
            $beforeInsertionEvent = new BeforeInsertionTreeEvent($this, $parent);
            $parent->dispatchEvent(TreeEvent::BEFORE_INSERTION, $beforeInsertionEvent);

            if ($beforeInsertionEvent->isCancelled()) {
                return;
            }
        }

        $this->parent = $parent;

        if ($parent && $addChildToParent) {
            $this->parent->addChild($this, false, false);

            if ($parent instanceof CompositeComponentInterface && $dispatchEvents) {
                $afterInsertionEvent = new AfterInsertionTreeEvent($this, $parent);
                $parent->dispatchEvent(TreeEvent::AFTER_INSERTION, $afterInsertionEvent);
            }
        }
    }

    /**
     * @see ComponentInterface::getOwnDependencies()
     */
    public function getOwnDependencies(): array
    {
        return [];
    }

    /**
     * @see ComponentInterface::getAdditionalDependencies()
     */
    public function getAdditionalDependencies(): array
    {
        return [];
    }

    /**
     * @see ComponentInterface::getDependencies()
     */
    public function getDependencies(): array
    {
        $dependencies = Helper::sortDependencies(
            array_merge(
                $this->getOwnDependencies(),
                $this->getAdditionalDependencies()
            ),
            $this
        );

        $event = new FilterDependenciesEvent($this, $dependencies);
        $this->dispatchEvent(FilterDependenciesEvent::EVENT_NAME, $event);

        return $event->getDependencies();
    }

    /**
     * @see ComponentInterface::getEventDispatcher()
     */
    public function getEventDispatcher(): EventDispatcherInterface
    {
        if (! $this->eventDispatcher) {
            $this->eventDispatcher = new EventDispatcher;
        }

        return $this->eventDispatcher;
    }

    /**
     * @see ComponentInterface::setEventDispatcher()
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @see ComponentInterface::on()
     */
    public function on(string $eventName, callable $listener): void
    {
        $this->getEventDispatcher()->addListener($eventName, $listener);
    }

    /**
     * @see ComponentInterface::off()
     */
    public function off(string $eventName, callable $listener): void
    {
        $this->getEventDispatcher()->removeListener($eventName, $listener);
    }

    /**
     * @see ComponentInterface::dispatchEvent()
     */
    public function dispatchEvent(string $eventName, Event $event, bool $capture = true, bool $bubbles = true): void
    {
        $parents = $this->getParents();

        if ($capture) {
            foreach (array_reverse($parents) as $parent) {
                $parent->getCaptureEventDispatcher()->dispatch($eventName, $event);
            }
        }

        $this->getEventDispatcher()->dispatch($eventName, $event);

        if ($bubbles) {
            foreach ($parents as $parent) {
                $parent->getEventDispatcher()->dispatch($eventName, $event);
            }
        }
    }

    /**
     * @see ComponentInterface::getAllData()
     */
    public function getAllData(): array
    {
        return $this->data;
    }

    /**
     * @see ComponentInterface::setData()
     */
    public function setData(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * @see ComponentInterface::getData()
     */
    public function getData(string $key)
    {
        return $this->data[$key] ?? null;
    }

    /**
     * @see ComponentInterface::hasData()
     */
    public function hasData(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * @see ComponentInterface::getTopData()
     */
    public function getTopData(string $key)
    {
        foreach ($this->parents() as $parent) {
            if ($parent->hasData($key)) {
                return $parent->getData($key);
            }
        }

        return null;
    }
}
