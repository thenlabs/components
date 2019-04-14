<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
interface ComponentWithNameInterface
{
    public function getName(): ?string;

    public function setName(?string $name): void;
}
