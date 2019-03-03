<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Tests;

use NubecuLabs\Components\CompositeComponentInterface;
use NubecuLabs\Components\CompositeComponentTrait;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class CompositeComponent implements CompositeComponentInterface
{
    use CompositeComponentTrait;

    public function getDependencies(): array
    {
        return [];
    }
}
