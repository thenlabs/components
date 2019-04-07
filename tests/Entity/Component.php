<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Tests\Entity;

use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\ComponentTrait;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class Component implements ComponentInterface
{
    use ComponentTrait, CommonTrait;
}
