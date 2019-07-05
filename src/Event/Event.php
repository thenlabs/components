<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Event;

use Symfony\Component\EventDispatcher\Event as SymfonyEvent;
use NubecuLabs\Components\ComponentInterface;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class Event extends SymfonyEvent
{
    /**
     * @var ComponentInterface|null
     */
    protected $source;

    /**
     * @return ComponentInterface|null
     */
    public function getSource(): ?ComponentInterface
    {
        return $this->source;
    }

    /**
     * @param ComponentInterface|null $source
     */
    public function setSource(?ComponentInterface $source): void
    {
        $this->source = $source;
    }
}
