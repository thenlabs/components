<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use NubecuLabs\Components\ComponentInterface;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
abstract class Helper
{
    public static function sortDependencies(array $dependencies, $conflictDispatcher = null, array $options = []): array
    {
        if ($conflictDispatcher && ! (
            $conflictDispatcher instanceof EventDispatcherInterface ||
            $conflictDispatcher instanceof ComponentInterface)
        ) {
            throw new Exception\InvalidConflictDispatcherException;
        }

        $result = [];

        foreach ($dependencies as $dependency) {
            if (! $dependency instanceof DependencyInterface) {
                continue;
            }

            self::addDependency($dependency, $result);
        }

        return $result;
    }

    private static function addDependency(DependencyInterface $dependency, array &$result): void
    {
        $dependencyName = $dependency->getName();

        // check if this dependency contains any of the already added.
        foreach ($dependency->getIncludeList() as $includedDep) {
            $name = $includedDep->getName();
            if (isset($result[$name])) {
                unset($result[$name]);
            }
        }

        // check if this dependency is implicit.
        foreach ($result as $resultDep) {
            if ($resultDep === $dependency) {
                return;
            }

            if ($resultDep->getName() == $dependencyName) {
                throw new Exception\UnresolvedDependencyConflictException($dependencyName);
            }

            foreach ($resultDep->getIncludeList() as $includedDep) {
                if ($includedDep->getName() == $dependency->getName()) {
                    return;
                }
            }
        }

        foreach ($dependency->getDependencies() as $dep) {
            self::addDependency($dep, $result);
        }

        $result[$dependency->getName()] = $dependency;
    }
}
