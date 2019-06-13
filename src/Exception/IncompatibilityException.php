<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Exception;

/**
 * This exception type is thrown when in a dependency conflict one of the parts
 * indicate explicitment that it's incompatible with the other.
 *
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class IncompatibilityException extends \Exception
{
    /**
     * Constructor.
     *
     * @param string $name     dependency name
     * @param string $version1 value of version
     * @param string $version2 value of version
     */
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
