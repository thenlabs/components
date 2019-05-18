<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\Event\Event;
use NubecuLabs\Components\Event\DependencyConflictEvent;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
abstract class Helper
{
    public static function sortDependencies(array $dependencies, $conflictDispatcher): array
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
        $eventName = Event::DEPENDENCY_CONFLICT . $name;
        $conflictEvent = new DependencyConflictEvent($dependency1, $dependency2);
        $conflictDispatcher->dispatch($eventName, $conflictEvent);

        $solution = $conflictEvent->getSolution();
        if (! $solution) {
            throw new Exception\UnresolvedDependencyConflictException($name);
        }

        return $solution;
    }
}
