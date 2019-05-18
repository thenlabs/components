<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Event;

use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\CompositeComponentInterface;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class TreeEvent extends Event
{
    public const BEFORE_INSERTION = 'components.tree.before_insertion';

    public const AFTER_INSERTION = 'components.tree.after_insertion';

    public const BEFORE_DELETION = 'components.tree.before_deletion';

    public const AFTER_DELETION = 'components.tree.after_deletion';

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
