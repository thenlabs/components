<?php
declare(strict_types=1);

namespace ThenLabs\Components\Event;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
trait CancellableTrait
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
