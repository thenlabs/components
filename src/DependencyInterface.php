<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
interface DependencyInterface extends DependentInterface
{
    /**
     * Returns the main name of the project. Example "jquery".
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Returns the value of the semantic version of the project.
     *
     * Should be an exactly version value. Example "1.11.1".
     *
     * @see https://getcomposer.org/doc/articles/versions.md
     *
     * @return string
     */
    public function getVersion(): string;

    /**
     * Returns the version constraint for which there are incompatibilities
     * with this dependency.
     *
     * @see https://getcomposer.org/doc/articles/versions.md
     *
     * @return string  Composer constraint.
     */
    public function getIncompatibleVersions(): string;

    /**
     * Returns all dependencies that are inside this dependency.
     *
     * @return DependencyInterace[]
     */
    public function getIncludedDependencies(): array;
}
