<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use NubecuLabs\Components\Event\Event;
use NubecuLabs\Components\Event\DependencyConflictEvent;
use Composer\Semver\Comparator;
use Composer\Semver\Semver;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
abstract class Helper
{
    public static function sortDependencies(array $dependencies, $conflictDispatcher): array
    {
        if ($conflictDispatcher && ! (
            $conflictDispatcher instanceof EventDispatcherInterface ||
            $conflictDispatcher instanceof ComponentInterface
        )
        ) {
            throw new Exception\InvalidConflictDispatcherException;
        }

        $result = [];

        foreach ($dependencies as $dependency) {
            if (! $dependency instanceof DependencyInterface) {
                continue;
            }

            self::addDependency($dependency, $result, $conflictDispatcher);
        }

        return $result;
    }

    private static function addDependency(DependencyInterface $dependency, array &$result, $conflictDispatcher): void
    {
        $dependencyName = $dependency->getName();

        // check if this dependency include any dependency that already is in result.
        // if true then remove it from result.
        foreach ($dependency->getIncludedDependencies() as $includedDep) {
            $name = $includedDep->getName();
            if (isset($result[$name])) {
                unset($result[$name]);
            }
        }

        foreach ($result as $resultDep) {
            // if the same instance already is in the result is not necesary do nothing.
            if ($resultDep === $dependency) {
                return;
            }

            if ($resultDep->getName() == $dependencyName) {
                $dependency = self::resolveConflict(
                    $resultDep,
                    $dependency,
                    $dependencyName,
                    $conflictDispatcher
                );
            }

            // check if dependency is implicit.
            foreach ($resultDep->getIncludedDependencies() as $includedDep) {
                if ($includedDep->getName() == $dependency->getName()) {
                    return;
                }
            }
        }

        foreach ($dependency->getDependencies() as $dep) {
            self::addDependency($dep, $result, $conflictDispatcher);
        }

        $result[$dependency->getName()] = $dependency;
    }

    private static function resolveConflict(DependencyInterface $dependency1, DependencyInterface $dependency2, string $name, $conflictDispatcher): DependencyInterface
    {
        if ($dependency1 === $dependency2) {
            return $dependency1;
        }

        $version1 = $dependency1->getVersion();
        $version2 = $dependency2->getVersion();
        $incompatibleVersions1 = $dependency1->getIncompatibleVersions();
        $incompatibleVersions2 = $dependency2->getIncompatibleVersions();

        if (($version1 && $incompatibleVersions2 && Semver::satisfies($version1, $incompatibleVersions2)) ||
            ($version2 && $incompatibleVersions1 && Semver::satisfies($version2, $incompatibleVersions1))
        ) {
            throw new Exception\IncompatibilityException($name, $version1, $version2);
        }

        $eventName = self::getConflictEventName($name);
        $conflictEvent = new DependencyConflictEvent($dependency1, $dependency2);

        if ($conflictDispatcher instanceof EventDispatcherInterface) {
            $conflictDispatcher->dispatch($eventName, $conflictEvent);
        } elseif ($conflictDispatcher instanceof ComponentInterface) {
            $conflictDispatcher->dispatchEvent($eventName, $conflictEvent);
        }

        $solution = $conflictEvent->getSolution();

        // if the conflict was not resolved then attempt resolve it automatically.
        if (! $solution && ($version1 && $version2)) {
            // the solution is the dependency with major version.
            $solution = Comparator::greaterThanOrEqualTo($version1, $version2) ?
                $dependency1 : $dependency2
            ;
        }

        if (! $solution) {
            throw new Exception\UnresolvedDependencyConflictException($name);
        }

        return $solution;
    }

    public static function getConflictEventName(string $name): string
    {
        return Event::DEPENDENCY_CONFLICT . $name;
    }
}
