<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

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
