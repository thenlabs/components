<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Tests\Entity;

use NubecuLabs\Components\CompositeComponentWithEventsInterface;
use NubecuLabs\Components\CompositeComponentWithEventsTrait;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class CompositeComponentWithEvents extends ComponentWithEvents implements CompositeComponentWithEventsInterface
{
    use CompositeComponentWithEventsTrait, CommonTrait;
}
