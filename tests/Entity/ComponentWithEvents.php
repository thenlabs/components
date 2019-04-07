<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Tests\Entity;

use NubecuLabs\Components\ComponentWithEventsInterface;
use NubecuLabs\Components\ComponentWithEventsTrait;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class ComponentWithEvents implements ComponentWithEventsInterface
{
    use ComponentWithEventsTrait, CommonTrait;
}
