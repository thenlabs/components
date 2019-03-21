<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
interface DependencyInterface
{
    /**
     * Include name and version. Example "jquery-1.11.1".
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Main name of the project. Example "jquery".
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Value of the semantic version of the project. Example "1.11.1".
     *
     * @return string
     */
    public function getVersion(): string;

    /**
     * Value of the semantic version of the project.
     *
     * @return string
     */
    public function getMinCompatibility(): string;

    public function getMaxCompatibility(): string;

    public function getMinAbsoluteCompatibility(): string;

    public function getMaxAbsoluteCompatibility(): string;
}
