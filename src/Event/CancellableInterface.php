<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Event;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
interface CancellableInterface
{
    /**
     * @return boolean
     */
    public function isCancelled(): bool;

    /**
     * Sets if operation is cancelled or not.
     *
     * @param  boolean $cancelled
     * @return void
     */
    public function cancel(bool $cancelled = true): void;
}
