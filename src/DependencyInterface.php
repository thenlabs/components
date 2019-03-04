<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
interface DependencyInterface
{
    public function getId(): string;

    public function getVersion(): string;
}
