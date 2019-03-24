<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
interface CompositeComponentInterface extends ComponentInterface
{
    public function hasChild($child): bool;

    public function addChild(ComponentInterface $child): void;

    public function getChild(string $id): ?ComponentInterface;

    public function dropChild($child): void;

    public function getChilds(): array;

    public function children(bool $recursive = true): iterable;

    public function getEventDispatcher(): EventDispatcherInterface;

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void;

    public function findChild(callable $callback, bool $recursive = true): ?ComponentInterface;

    public function findChilds(callable $callback, bool $recursive = true): array;

    public function findChildById(string $id): ?ComponentInterface;

    public function on(string $eventName, callable $listener): void;

    public function getOwnDependencies(): array;
}
