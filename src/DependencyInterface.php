<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
interface DependencyInterface extends DependentInterface
{
    /**
     * Main name of the project. Example "jquery".
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Value of the semantic version of the project.
     *
     * Should be an exactly version. Example "1.11.1".
     *
     * @return string
     */
    public function getVersion(): string;

    public function getUpgradeableVersionsList(): array;

    public function getIncompatibilityVersionsList(): array;

    public function getIncludedDependencies(): array;
}
