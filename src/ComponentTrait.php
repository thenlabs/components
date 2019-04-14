<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

use NubecuLabs\Components\Event\TreeEvent;
use NubecuLabs\Components\Event\AfterInsertionTreeEvent;
use NubecuLabs\Components\Event\AfterDeletionTreeEvent;
use NubecuLabs\Components\Event\BeforeInsertionTreeEvent;
use NubecuLabs\Components\Event\BeforeDeletionTreeEvent;

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

    public function getParents(): array
    {
        $parents = [];

        foreach ($this->parents() as $parent) {
            $parents[] = $parent;
        }

        return $parents;
    }

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

    public function setParent(?CompositeComponentInterface $parent, bool $addChildToParent = true, bool $dispatchEvents = true): void
    {
        if ($this->parent instanceof CompositeComponentInterface) {
            if ($this->parent instanceof CompositeComponentWithEventsInterface && $dispatchEvents) {
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

            if ($oldParent instanceof CompositeComponentWithEventsInterface && $dispatchEvents) {
                $afterDeletionEvent = new AfterDeletionTreeEvent($this, $oldParent);
                $oldParent->getEventDispatcher()->dispatch(
                    TreeEvent::AFTER_DELETION,
                    $afterDeletionEvent
                );
            }
        }

        if ($parent instanceof CompositeComponentWithEventsInterface && $dispatchEvents) {
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

            if ($parent instanceof CompositeComponentWithEventsInterface && $dispatchEvents) {
                $afterInsertionEvent = new AfterInsertionTreeEvent($this, $parent);
                $parent->getEventDispatcher()->dispatch(
                    TreeEvent::AFTER_INSERTION,
                    $afterInsertionEvent
                );
            }
        }
    }

    public function getOwnDependencies(): array
    {
        return [];
    }

    public function getAdditionalDependencies(): array
    {
        return [];
    }

    public function getDependencies(): array
    {
        return [];
    }
}
