<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Event;

use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\CompositeComponentInterface;

/**
 * The tree events including a parent and a child like members.
 *
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class TreeEvent extends Event
{
    /**
     * @var ComponentInterface
     */
    protected $child;

    /**
     * @var CompositeComponentInterface
     */
    protected $parent;

    /**
     * Constructor.
     *
     * @param ComponentInterface          $child
     * @param CompositeComponentInterface $parent
     */
    public function __construct(ComponentInterface $child, CompositeComponentInterface $parent)
    {
        $this->child = $child;
        $this->parent = $parent;
    }

    /**
     * @return ComponentInterface
     */
    public function getChild(): ComponentInterface
    {
        return $this->child;
    }

    /**
     * @return CompositeComponentInterface
     */
    public function getParent(): CompositeComponentInterface
    {
        return $this->parent;
    }
}
