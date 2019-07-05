<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Event;

use NubecuLabs\Components\DependencyInterface;
use NubecuLabs\Components\ComponentInterface;

/**
 * This event type is throwns with the calls to the methods
 * ComponentTrait::getDependencies() and CompositeComponentTrait::getDependencies()
 *
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class FilterDependenciesEvent extends Event
{
    /**
     * @var ComponentInterface
     */
    protected $component;

    /**
     * @var DependencyInterface[]
     */
    protected $dependencies;

    /**
     * Constructor.
     *
     * @param DependencyInterface[]
     */
    public function __construct(ComponentInterface $component, array $dependencies)
    {
        $this->component = $component;
        $this->dependencies = $dependencies;
    }

    /**
     * @param DependencyInterface[]
     */
    public function setDependencies(array $dependencies): void
    {
        $this->dependencies = $dependencies;
    }

    /**
     * @return DependencyInterface[]
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    /**
     * @return ComponentInterface
     */
    public function getComponent(): ComponentInterface
    {
        return $this->component;
    }
}
