<?php
declare(strict_types=1);

namespace ThenLabs\Components;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use ThenLabs\Components\Event\AfterDeletionEvent;
use ThenLabs\Components\Event\AfterInsertionEvent;
use ThenLabs\Components\Event\BeforeDeletionEvent;
use ThenLabs\Components\Event\BeforeInsertionEvent;
use ThenLabs\Components\Event\Event;
use ThenLabs\Components\Event\FilterDependenciesEvent;

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
    public function setName(?string $name)
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
    public function setParent(?CompositeComponentInterface $parent, bool $addChildToParent = true, bool $dispatchEvents = true)
    {
        if ($this->parent instanceof CompositeComponentInterface) {
            if ($dispatchEvents) {
                $beforeDeletionEvent = new BeforeDeletionEvent;
                $beforeDeletionEvent->setChild($this);
                $beforeDeletionEvent->setParent($this->parent);

                $this->parent->dispatchEvent(BeforeDeletionEvent::class, $beforeDeletionEvent);

                if ($beforeDeletionEvent->isCancelled()) {
                    return;
                }
            }

            $oldParent = $this->parent;
            $this->parent->dropChild($this, false, false);

            if ($oldParent instanceof CompositeComponentInterface && $dispatchEvents) {
                $afterDeletionEvent = new AfterDeletionEvent;
                $afterDeletionEvent->setChild($this);
                $afterDeletionEvent->setParent($oldParent);

                $oldParent->dispatchEvent(AfterDeletionEvent::class, $afterDeletionEvent);
            }
        }

        if ($parent instanceof CompositeComponentInterface && $dispatchEvents) {
            $beforeInsertionEvent = new BeforeInsertionEvent;
            $beforeInsertionEvent->setChild($this);
            $beforeInsertionEvent->setParent($parent);

            $parent->dispatchEvent(BeforeInsertionEvent::class, $beforeInsertionEvent);

            if ($beforeInsertionEvent->isCancelled()) {
                return;
            }
        }

        $this->parent = $parent;

        if ($parent && $addChildToParent) {
            $this->parent->addChild($this, false, false);

            if ($parent instanceof CompositeComponentInterface && $dispatchEvents) {
                $afterInsertionEvent = new AfterInsertionEvent;
                $afterInsertionEvent->setChild($this);
                $afterInsertionEvent->setParent($parent);

                $parent->dispatchEvent(AfterInsertionEvent::class, $afterInsertionEvent);
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
        $eventName = FilterDependenciesEvent::class . "_{$this->getId()}";
        $this->dispatchEvent($eventName, $event);

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
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @see ComponentInterface::on()
     */
    public function on(string $eventName, callable $listener)
    {
        $this->getEventDispatcher()->addListener($eventName, $listener);
    }

    /**
     * @see ComponentInterface::off()
     */
    public function off(string $eventName, callable $listener)
    {
        $this->getEventDispatcher()->removeListener($eventName, $listener);
    }

    /**
     * @see ComponentInterface::dispatchEvent()
     */
    public function dispatchEvent(string $eventName, Event $event, bool $capture = true, bool $bubbles = true)
    {
        $event->setSource($this);

        $parents = $this->getParents();

        if ($capture) {
            $captureEvent = clone $event;
            $captureEvent->setTarget($this);

            foreach (array_reverse($parents) as $parent) {
                $captureEvent->setSource($parent);

                $parent->getCaptureEventDispatcher()->dispatch($captureEvent, $eventName);
            }
        }

        $this->getEventDispatcher()->dispatch($event, $eventName);

        if ($bubbles) {
            foreach ($parents as $parent) {
                $parent->getEventDispatcher()->dispatch($event, $eventName);
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
    public function setData(string $key, $value)
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
    public function getTopData(string $key, bool $currentFirst = true)
    {
        $list = $this->getParents();

        if ($currentFirst) {
            array_unshift($list, $this);
        }

        foreach ($list as $item) {
            if ($item->hasData($key)) {
                return $item->getData($key);
            }
        }

        return null;
    }
}
