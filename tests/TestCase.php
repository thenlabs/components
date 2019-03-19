<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Andaniel05\PyramidalTests\Utils\StaticVarsInjectionTrait;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class TestCase extends PHPUnitTestCase
{
    use StaticVarsInjectionTrait;
}
