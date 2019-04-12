<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Tests\Entity;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
trait CommonTrait
{
    /**
     * Permit assign the id for testing purpouses.
     */
    public function __construct(?string $id = null)
    {
        if ($id) {
            $this->id = $id;
        }
    }

    /**
     * Permit assign the dependencies for testing purpouses.
     */
    // public function setDependencies(array $dependencies): void
    // {
    //     $this->dependencies = $dependencies;
    // }
}
