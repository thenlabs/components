<?php
declare(strict_types=1);

namespace ThenLabs\Components\Event;

use ThenLabs\Components\ComponentInterface;
use ThenLabs\Components\CompositeComponentInterface;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
trait ParentChildTrait
{
    /**
     * @param ComponentInterface $child
     */
    protected $child;

    /**
     * @return CompositeComponentInterface
     */
    protected $parent;

    /**
     * @return ComponentInterface|null
     */
    public function getChild(): ?ComponentInterface
    {
        return $this->child;
    }

    /**
     * @param ComponentInterface|null $child
     */
    public function setChild(?ComponentInterface $child): void
    {
        $this->child = $child;
    }

    /**
     * @return CompositeComponentInterface|null
     */
    public function getParent(): ?CompositeComponentInterface
    {
        return $this->parent;
    }

    /**
     * @param CompositeComponentInterface|null $parent
     */
    public function setParent(?CompositeComponentInterface $parent): void
    {
        $this->parent = $parent;
    }
}
