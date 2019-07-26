<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Event;

use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\CompositeComponentInterface;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
interface ParentChildInterface
{
    /**
     * @return ComponentInterface|null
     */
    public function getChild(): ?ComponentInterface;

    /**
     * @param ComponentInterface|null $child
     */
    public function setChild(?ComponentInterface $child): void;

    /**
     * @return CompositeComponentInterface|null
     */
    public function getParent(): ?CompositeComponentInterface;

    /**
     * @param CompositeComponentInterface|null $parent
     */
    public function setParent(?CompositeComponentInterface $parent): void;
}
