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
        if (! $this->validateChild($child)) {
            throw new Exception\InvalidChildException(
                "Invalid child with id equal to '{$child->getId()}'."
            );
        }

        $this->childs[$child->getId()] = $child;

        if ($setParentInChild) {
            $child->setParent($this, false, false);
        }
    }

    public function addChilds(ComponentInterface ...$childs): void
    {
        foreach ($childs as $child) {
            $this->addChild($child);
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
            $obj->setParent(null, false, false);
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

    public function validateChild(ComponentInterface $child): bool
    {
        return true;
    }
}
