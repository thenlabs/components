<?php
declare(strict_types=1);

namespace ThenLabs\Components\Tests\Entity;

use ThenLabs\Components\ComponentInterface;
use ThenLabs\Components\ComponentTrait;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class Component implements ComponentInterface
{
    use ComponentTrait;

    /**
     * Permit assign the id for testing purpouses.
     */
    public function __construct(?string $id = null)
    {
        if ($id) {
            $this->id = $id;
            $this->name = $id;
        }
    }
}
