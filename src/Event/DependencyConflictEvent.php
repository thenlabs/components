<?php
declare(strict_types=1);

namespace ThenLabs\Components\Event;

use ThenLabs\Components\DependencyInterface;

/**
 * This event type it's triggered when two component has two dependencies with equal name.
 *
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class DependencyConflictEvent extends Event
{
    /**
     * @var DependencyInterface
     */
    protected $dependency1;

    /**
     * @var DependencyInterface
     */
    protected $dependency2;

    /**
     * Is the solution of the conflict.
     *
     * @var DependencyInterface
     */
    protected $solution;

    /**
     * Constructor.
     *
     * @param DependencyInterface $dependency1
     * @param DependencyInterface $dependency2
     */
    public function __construct(DependencyInterface $dependency1, DependencyInterface $dependency2)
    {
        $this->dependency1 = $dependency1;
        $this->dependency2 = $dependency2;
    }

    /**
     * @return DependencyInterface|null
     */
    public function getSolution(): ?DependencyInterface
    {
        return $this->solution;
    }

    /**
     * @param DependencyInterface $solution
     */
    public function setSolution(DependencyInterface $solution): void
    {
        $this->solution = $solution;
    }

    /**
     * @return DependencyInterface|null
     */
    public function getDependency1(): ?DependencyInterface
    {
        return $this->dependency1;
    }

    /**
     * @return DependencyInterface|null
     */
    public function getDependency2(): ?DependencyInterface
    {
        return $this->dependency2;
    }
}
