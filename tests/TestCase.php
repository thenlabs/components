<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Andaniel05\PyramidalTests\Utils\StaticVarsInjectionTrait;
use NubecuLabs\Components\ComponentInterface;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class TestCase extends PHPUnitTestCase
{
    use StaticVarsInjectionTrait;

    public function getNewComponent(): ComponentInterface
    {
        return new $this->componentClass;
    }

    public function getRandomArray(string $prefix = ''): array
    {
        $result = [];

        foreach (range(1, mt_rand(1, 10)) as $i) {
            $result[] = $prefix . $i;
        }

        return $result;
    }
}
