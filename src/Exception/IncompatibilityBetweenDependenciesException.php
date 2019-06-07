<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Exception;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\DependencyInterface;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class IncompatibilityBetweenDependenciesException extends \Exception
{
    public function __construct(string $name, string $version1, string $version2)
    {
        parent::__construct(sprintf(
            "The dependency '{$name}' version '{$version1}' is not compatible with version '{$version2}'.",
            $name,
            $version1,
            $version2
        ));
    }
}
