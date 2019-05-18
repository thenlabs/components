<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Event;

use Symfony\Component\EventDispatcher\Event as SymfonyEvent;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class Event extends SymfonyEvent
{
    public const DEPENDENCY_CONFLICT = 'components.dependency_conflict_';
}
