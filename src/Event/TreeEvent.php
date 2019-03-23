<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Event;

use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\CompositeComponentInterface;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class TreeEvent
{
    protected $child;

    protected $parent;

    public function __construct(ComponentInterface $child, CompositeComponentInterface $parent)
    {
        $this->child = $child;
        $this->parent = $parent;
    }

    public function getChild(): ComponentInterface
    {
        return $this->child;
    }

    public function getParent(): CompositeComponentInterface
    {
        return $this->parent;
    }
}
