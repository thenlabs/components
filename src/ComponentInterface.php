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

    public function getParents(): array;

    public function parents(): iterable;

    public function setParent(?CompositeComponentInterface $parent, bool $addChildToParent = true, bool $dispatchEvents = true): void;

    public function getOwnDependencies(): array;

    public function getAdditionalDependencies(): array;

    public function getDependencies(): array;
}
