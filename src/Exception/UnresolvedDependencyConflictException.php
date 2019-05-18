<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Exception;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class UnresolvedDependencyConflictException extends \Exception
{
    public function __construct(string $name)
    {
        parent::__construct("Conflict between dependencies with name '{$name}'.");
    }
}
