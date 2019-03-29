<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

use NubecuLabs\Components\Event\TreeEvent;
use NubecuLabs\Components\Event\AfterInsertionTreeEvent;
use NubecuLabs\Components\Event\BeforeInsertionTreeEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
trait ComponentTrait
{
    private $id;

    protected $parent;

    protected $dependencies = [];

    protected $eventDispatcher;

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

    public function getParents(): array
    {
        $result = [];

        $node = $this;
        while ($node) {
            $parent = $node->getParent();
            if ($parent) {
                $result[] = $parent;
            }

            $node = $parent;
        }

        return $result;
    }

    public function setParent(?CompositeComponentInterface $parent, bool $addChildToParent = true, array $eventsConfig = []): void
    {
        $eventsConfigDefault = [
            'before_insertion' => true,
            'after_insertion' => true,
        ];

        $eventsConfig = array_merge($eventsConfigDefault, $eventsConfig);

        if ($parent && $eventsConfig['before_insertion']) {
            $beforeInsertionEvent = new BeforeInsertionTreeEvent($this, $parent);
            $parent->getEventDispatcher()->dispatch(TreeEvent::BEFORE_INSERTION, $beforeInsertionEvent);

            if ($beforeInsertionEvent->isCancelled()) {
                return;
            }
        }

        if ($this->parent instanceof CompositeComponentInterface) {
            $this->parent->dropChild($this);
        }

        $this->parent = $parent;

        if ($parent && $addChildToParent) {
            $this->parent->addChild($this, false, [
                'before_insertion' => false,
                'after_insertion' => false,
            ]);
        }

        if ($parent && $eventsConfig['after_insertion']) {
            $afterInsertionEvent = new AfterInsertionTreeEvent($this, $parent);
            $parent->getEventDispatcher()->dispatch(TreeEvent::AFTER_INSERTION, $afterInsertionEvent);
        }
    }

    public function detach(): void
    {
        $this->setParent(null);
    }

    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function getEventDispatcher(): EventDispatcherInterface
    {
        if (! $this->eventDispatcher) {
            $this->eventDispatcher = new EventDispatcher;
        }

        return $this->eventDispatcher;
    }

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function on(string $eventName, callable $listener): void
    {
        $this->getEventDispatcher()->addListener($eventName, $listener);
    }

    public function off(string $eventName, callable $listener): void
    {
        $this->getEventDispatcher()->removeListener($eventName, $listener);
    }

    public function dispatch(string $eventName, Event $event, bool $capture = true, bool $bubbles = true): void
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
}
