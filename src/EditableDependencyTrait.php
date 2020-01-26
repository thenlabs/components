<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
trait EditableDependencyTrait
{
    protected $name;
    protected $version = '';
    protected $incompatibleVersions;
    protected $includedDependencies = [];
    protected $dependencies = [];

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    public function getIncompatibleVersions(): ?string
    {
        return $this->incompatibleVersions;
    }

    public function setIncompatibleVersions(?string $incompatibleVersions): void
    {
        $this->incompatibleVersions = $incompatibleVersions;
    }

    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function setDependencies(array $dependencies): void
    {
        $this->dependencies = $dependencies;
    }

    public function getIncludedDependencies(): array
    {
        return $this->includedDependencies;
    }

    public function setIncludedDependencies(array $includedDependencies): void
    {
        $this->includedDependencies = $includedDependencies;
    }
}
