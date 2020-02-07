<?php
declare(strict_types=1);

namespace ThenLabs\Components\Tests\Entity;

use ThenLabs\Components\CompositeComponentInterface;
use ThenLabs\Components\CompositeComponentTrait;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class CompositeComponent extends Component implements CompositeComponentInterface
{
    use CompositeComponentTrait;
}
