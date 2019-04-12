<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

use NubecuLabs\Components\Event\TreeEvent;
use NubecuLabs\Components\Event\AfterInsertionTreeEvent;
use NubecuLabs\Components\Event\AfterDeletionTreeEvent;
use NubecuLabs\Components\Event\BeforeInsertionTreeEvent;
use NubecuLabs\Components\Event\BeforeDeletionTreeEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
trait ComponentWithEventsTrait
{
    use ComponentTrait;

    protected $eventDispatcher;

    public function setParent(?CompositeComponentInterface $parent, bool $addChildToParent = true, bool $dispatchEvents = true): void
    {
        if ($this->parent instanceof CompositeComponentInterface) {
            if ($dispatchEvents) {
                $beforeDeletionEvent = new BeforeDeletionTreeEvent($this, $this->parent);
                $this->parent->getEventDispatcher()->dispatch(
                    TreeEvent::BEFORE_DELETION,
                    $beforeDeletionEvent
                );

                if ($beforeDeletionEvent->isCancelled()) {
                    return;
                }
            }

            $oldParent = $this->parent;
            $this->parent->dropChild($this, false, false);

            if ($dispatchEvents) {
                $afterDeletionEvent = new AfterDeletionTreeEvent($this, $oldParent);
                $oldParent->getEventDispatcher()->dispatch(
                    TreeEvent::AFTER_DELETION,
                    $afterDeletionEvent
                );
            }
        }

        if ($parent instanceof CompositeComopnentWithEventsInterface && $dispatchEvents) {
            $beforeInsertionEvent = new BeforeInsertionTreeEvent($this, $parent);
            $parent->getEventDispatcher()->dispatch(
                TreeEvent::BEFORE_INSERTION,
                $beforeInsertionEvent
            );

            if ($beforeInsertionEvent->isCancelled()) {
                return;
            }
        }

        $this->parent = $parent;

        if ($parent && $addChildToParent) {
            $this->parent->addChild($this, false, false);

            if ($dispatchEvents) {
                $afterInsertionEvent = new AfterInsertionTreeEvent($this, $parent);
                $parent->getEventDispatcher()->dispatch(
                    TreeEvent::AFTER_INSERTION,
                    $afterInsertionEvent
                );
            }
        }
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
