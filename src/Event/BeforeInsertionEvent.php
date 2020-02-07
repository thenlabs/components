<?php
declare(strict_types=1);

namespace ThenLabs\Components\Event;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class BeforeInsertionEvent extends Event implements ParentChildInterface, CancellableInterface
{
    use ParentChildTrait, CancellableTrait;
}
