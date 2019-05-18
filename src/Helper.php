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

        // check if this dependency contains any of the already added.
        foreach ($dependency->getIncludeList() as $includedDep) {
            $name = $includedDep->getName();
            if (isset($result[$name])) {
                unset($result[$name]);
            }
        }

        foreach ($result as $resultDep) {
            if ($resultDep === $dependency) {
                return;
            }

            if ($resultDep->getName() == $dependencyName) {
                $eventName = Event::DEPENDENCY_CONFLICT . $dependencyName;
                $conflictEvent = new DependencyConflictEvent($resultDep, $dependency);
                $conflictDispatcher->dispatch($eventName, $conflictEvent);

                $dependency = $conflictEvent->getSolution();
                if (! $dependency) {
                    throw new Exception\UnresolvedDependencyConflictException($dependencyName);
                }
            }

            // check if dependency is implicit.
            foreach ($resultDep->getIncludeList() as $includedDep) {
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
}
