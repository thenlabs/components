<?php
declare(strict_types=1);

namespace ThenLabs\Components\Event;

use ThenLabs\Components\ComponentInterface;

if (class_exists('Symfony\Contracts\EventDispatcher\Event')) {
    class BaseEvent extends \Symfony\Contracts\EventDispatcher\Event
    {
    }
} else {
    class BaseEvent extends \Symfony\Component\EventDispatcher\Event
    {
    }
}

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class Event extends BaseEvent
{
    /**
     * @var ComponentInterface|null
     */
    protected $source;

    /**
     * @var ComponentInterface|null
     */
    protected $target;

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

    /**
     * @return ComponentInterface|null
     */
    public function getTarget(): ?ComponentInterface
    {
        return $this->target;
    }

    /**
     * @param ComponentInterface|null $target
     */
    public function setTarget(?ComponentInterface $target): void
    {
        $this->target = $target;
    }
}
