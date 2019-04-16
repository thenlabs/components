<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Tests\Entity;

use NubecuLabs\Components\CompositeComponentInterface;
use NubecuLabs\Components\CompositeComponentTrait;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class CompositeComponent extends Component implements CompositeComponentInterface
{
    use CompositeComponentTrait;
}
