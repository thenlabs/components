<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Exception;

/**
 * This exception type is thrown after that a dependency conflict event was
 * dispatched and there was no solution.
 *
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class UnresolvedDependencyConflictException extends \Exception
{
    /**
     * Constuctor.
     *
     * @param string $name dependency name
     */
    public function __construct(string $name)
    {
        parent::__construct("Conflict between dependencies with name '{$name}'.");
    }
}
