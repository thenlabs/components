<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Event;

use NubecuLabs\Components\DependencyInterface;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class DependencyConflictEvent extends Event
{
    protected $dependency1;

    protected $dependency2;

    protected $solution;

    public function __construct(DependencyInterface $dependency1, DependencyInterface $dependency2)
    {
        $this->dependency1 = $dependency1;
        $this->dependency2 = $dependency2;
    }

    public function getSolution(): ?DependencyInterface
    {
        return $this->solution;
    }

    public function setSolution(DependencyInterface $solution): void
    {
        $this->solution = $solution;
    }

    public function getDependency1(): ?DependencyInterface
    {
        return $this->dependency1;
    }

    public function getDependency2(): ?DependencyInterface
    {
        return $this->dependency2;
    }
}
