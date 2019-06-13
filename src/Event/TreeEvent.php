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
     * Occurs before a new component it's inserted in the tree.
     */
    public const BEFORE_INSERTION = 'components.tree.before_insertion';

    /**
     * Occurs after a new component was inserted in the tree.
     */
    public const AFTER_INSERTION = 'components.tree.after_insertion';

    /**
     * Occurs before that a component of the tree it's detached.
     */
    public const BEFORE_DELETION = 'components.tree.before_deletion';

    /**
     * Occurs after that a component of the tree was detached.
     */
    public const AFTER_DELETION = 'components.tree.after_deletion';

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
