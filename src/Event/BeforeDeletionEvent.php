<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Event;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class BeforeDeletionEvent extends Event implements ParentChildInterface, CancellableInterface
{
    use ParentChildTrait, CancellableTrait;
}
