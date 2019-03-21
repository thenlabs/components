<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
interface ComponentInterface
{
    public function getId(): string;

    public function getParent(): ?CompositeComponentInterface;

    public function setParent(?CompositeComponentInterface $parent, bool $addChild = true): void;

    public function detach(): void;

    public function getDependencies(): array;

    public function getOwnDependencies(): array;
}
