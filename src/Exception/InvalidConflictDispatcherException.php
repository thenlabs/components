<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Exception;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use NubecuLabs\Components\ComponentInterface;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class InvalidConflictDispatcherException extends \Exception
{
    public function __construct()
    {
        parent::__construct(sprintf(
            'The conflict dispatcher may be only an instance of "%s" or "%s".',
            EventDispatcherInterface::class,
            ComponentInterface::class
        ));
    }
}
