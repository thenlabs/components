<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Event;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 * @abstract
 */
abstract class AbstractCancellableTreeEvent extends TreeEvent
{
    protected $cancelled = false;

    public function isCancelled(): bool
    {
        return $this->cancelled;
    }

    public function cancel(bool $cancelled = true): void
    {
        $this->cancelled = $cancelled;
    }
}
