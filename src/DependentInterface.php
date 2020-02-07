<?php
declare(strict_types=1);

namespace ThenLabs\Components;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
interface DependentInterface
{
    /**
     * @return DependencyInterface
     */
    public function getDependencies(): array;
}
