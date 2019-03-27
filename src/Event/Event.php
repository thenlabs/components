<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Event;

use NubecuLabs\Components\ComponentInterface;
use Symfony\Component\EventDispatcher\Event as SymfonyEvent;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class Event extends SymfonyEvent
{
    protected $source;

    public function __construct(ComponentInterface $source)
    {
        $this->source = $source;
    }

    public function getSource(): ComponentInterface
    {
        return $this->source;
    }
}
