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
    public function getId(): string;

    public function getParent(): ?CompositeComponentInterface;

    public function getParents(): array;

    public function parents(): iterable;

    public function setParent(?CompositeComponentInterface $parent, bool $addChildToParent = true, bool $dispatchEvents = true): void;

    public function getOwnDependencies(array $options = []): array;

    public function getAdditionalDependencies(array $options = []): array;

    public function getEventDispatcher(): EventDispatcherInterface;

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void;

    public function on(string $eventName, callable $listener): void;

    public function off(string $eventName, callable $listener): void;

    public function dispatch(string $eventName, Event $event, bool $capture = true, bool $bubbles = true): void;
}
