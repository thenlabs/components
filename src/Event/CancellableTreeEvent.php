<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Event;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class CancellableTreeEvent extends TreeEvent
{
    /**
     * Indicate if operation should be cancelled or not.
     *
     * @var boolean
     */
    protected $cancelled = false;

    /**
     * @return boolean
     */
    public function isCancelled(): bool
    {
        return $this->cancelled;
    }

    /**
     * Sets if operation is cancelled or not.
     *
     * @param  boolean $cancelled
     * @return void
     */
    public function cancel(bool $cancelled = true): void
    {
        $this->cancelled = $cancelled;
    }
}
