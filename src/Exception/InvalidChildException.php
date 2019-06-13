<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Exception;

/**
 * This exception type is thrown when in a composite component adds a child
 * that type is not permited in the parent.
 *
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class InvalidChildException extends \TypeError
{
}
