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

        $add = function ($dependency) use (&$result, &$add) {
            // check if this dependency contains any of the already added.
            foreach ($dependency->getIncludeList() as $dep) {
                $name = $dep->getName();
                if (isset($result[$name])) {
                    unset($result[$name]);
                }
            }

            // check if dependency is implicit.
            foreach ($result as $dep) {
                $include = $dep->getIncludeList();
                if (isset($include[$dependency->getName()])) {
                    return;
                }
            }

            foreach ($dependency->getDependencies() as $dep) {
                $add($dep);
            }

            $result[$dependency->getName()] = $dependency;
        };

        foreach ($dependencies as $dependency) {
            if (! $dependency instanceof DependencyInterface) {
                continue;
            }

            $add($dependency);
        }

        return $result;
    }
}
