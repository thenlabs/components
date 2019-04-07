<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
interface CompositeComponentWithEventsInterface extends CompositeComponentInterface
{
    public function addChild(ComponentInterface $child, $setParentInChild = true, bool $dispatchEvents = true): void;

    public function dropChild($child, bool $dispatchEvents = true): void;

    public function getCaptureEventDispatcher(): EventDispatcherInterface;

    public function setCaptureEventDispatcher(EventDispatcherInterface $eventDispatcher): void;

    public function on(string $eventName, callable $listener, bool $capture = false): void;

    public function off(string $eventName, callable $listener, bool $capture = false): void;
}
