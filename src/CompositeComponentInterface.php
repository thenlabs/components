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

    public function getOwnChilds(): array;

    public function children(): iterable;

    public function getEventDispatcher(): EventDispatcherInterface;
}
