<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Event;

use Symfony\Component\EventDispatcher\Event;
use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\CompositeComponentInterface;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class TreeEvent extends Event
{
    public const BEFORE_INSERTION = 'tree.before_insertion';

    public const AFTER_INSERTION = 'tree.after_insertion';

    public const BEFORE_DELETION = 'tree.before_deletion';

    public const AFTER_DELETION = 'tree.after_deletion';

    protected $child;

    protected $parent;

    public function __construct(ComponentInterface $source, ComponentInterface $child, CompositeComponentInterface $parent)
    {
        parent::__construct($source);

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