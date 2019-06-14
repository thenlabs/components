<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
interface ComponentInterface extends DependentInterface
{
    /**
     * Returns the component identifier.
     *
     * The component identifier is a value
     *
     * @return string
     */
    public function getId(): string;

    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getParent(): ?CompositeComponentInterface;

    public function getParents(): array;

    public function parents(): iterable;

    public function setParent(?CompositeComponentInterface $parent, bool $addChildToParent = true, bool $dispatchEvents = true): void;

    public function getOwnDependencies(): array;

    public function getAdditionalDependencies(): array;

    public function getEventDispatcher(): EventDispatcherInterface;

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void;

    public function on(string $eventName, callable $listener): void;

    public function off(string $eventName, callable $listener): void;

    public function dispatchEvent(string $eventName, Event $event, bool $capture = true, bool $bubbles = true): void;

    public function getAllData(): array;

    public function setData(string $key, $value): void;

    public function getData(string $key);

    public function hasData(string $key): bool;

    public function getTopData(string $key);
}
