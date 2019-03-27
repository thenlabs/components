<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

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

    public function setParent(?CompositeComponentInterface $parent, bool $addChildToParent = true): void
    {
        if ($this->parent instanceof CompositeComponentInterface) {
            $this->parent->dropChild($this);
        }

        $this->parent = $parent;

        if ($parent && $addChildToParent) {
            $this->parent->addChild($this, false);
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
        $this->getEventDispatcher()->dispatch($eventName, $event);
    }
}
