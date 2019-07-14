<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Base component contract.
 *
 * This type of component may contains others components.
 *
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
interface CompositeComponentInterface extends ComponentInterface
{
    /**
     * Checks if the component has a direct child.
     *
     * @param  ComponentInterface|string  $child  If the value is a string then searchs by the component id.
     * @return boolean
     */
    public function hasChild($child): bool;

    /**
     * Adds several direct childs at once.
     *
     * @param ComponentInterface[] $childs
     */
    public function addChilds(ComponentInterface ...$childs): void;

    /**
     * Adds a direct child.
     *
     * @param ComponentInterface $child
     * @param boolean            $setParentInChild Indicates whether this component should be established as the child's parent.
     * @param boolean            $dispatchEvents   Indicates if the operation will trigger events.
     */
    public function addChild(ComponentInterface $child, $setParentInChild = true, bool $dispatchEvents = true): void;

    /**
     * Removes a direct child.
     *
     * @param  ComponentInterface|string $child
     * @param  boolean                   $dispatchEvents  Indicates if the operation will trigger events.
     */
    public function dropChild($child, bool $dispatchEvents = true): void;

    /**
     * Returns a direct child.
     *
     * @param  string $id
     * @return ComponentInterface|null
     */
    public function getChild(string $id): ?ComponentInterface;

    /**
     * Returns all the direct child components.
     *
     * @return ComponentInterface[]
     */
    public function getChilds(): array;

    /**
     * Use this method for iterate over each child of the tree in deep.
     *
     * If the deep argument is false then the iteration is only over the direct childs.
     *
     * @param  boolean  $deep
     * @return iterable
     */
    public function children(bool $deep = true): iterable;

    /**
     * Searchs a child in the tree using a callback for the evaluation.
     *
     * Returns the first child for which the callback returns a true value.
     *
     * @param  callable  $callback
     * @param  boolean   $deep
     * @return ComponentInterface|null
     */
    public function findChild(callable $callback, bool $deep = true): ?ComponentInterface;

    /**
     * Searchs childs in the tree using a callback for the evaluation.
     *
     * Returns all the childs for which the callback returns a true value.
     *
     * @param  callable $callback
     * @param  boolean  $deep
     * @return ComponentInterface[]
     */
    public function findChilds(callable $callback, bool $deep = true): array;

    /**
     * Searchs a child by his identifier.
     *
     * @param  string $id
     * @return ComponentInterface|null
     */
    public function findChildById(string $id): ?ComponentInterface;

    /**
     * Searchs a child by his name.
     *
     * @param  string $name
     * @return ComponentInterface|null
     */
    public function findChildByName(string $name): ?ComponentInterface;

    /**
     * Searchs childs by name.
     *
     * @param  string $name
     * @return ComponentInterface[]
     */
    public function findChildsByName(string $name): array;

    /**
     * Validates if a child may be inserted in this component.
     *
     * @param  ComponentInterface $child
     * @return boolean
     */
    public function validateChild(ComponentInterface $child): bool;

    /**
     * Returns the event dispatcher for capturing.
     *
     * @return EventDispatcherInterface
     */
    public function getCaptureEventDispatcher(): EventDispatcherInterface;

    /**
     * Sets the event dispatcher for capturing.
     *
     * @param EventDispatcherInterface
     */
    public function setCaptureEventDispatcher(EventDispatcherInterface $eventDispatcher): void;

    /**
     * @see ComponentInterface::on()
     *
     * @param  string   $eventName
     * @param  callable $listener
     * @param  boolean  $capture  Indicates if event is for capturing or not.
     */
    public function on(string $eventName, callable $listener, bool $capture = false): void;

    /**
     * @see ComponentInterface::off()
     *
     * @param  string   $eventName
     * @param  callable $listener
     * @param  boolean  $capture  Indicates if event is for capturing or not.
     */
    public function off(string $eventName, callable $listener, bool $capture = false): void;

    /**
     * Returns an array of string with the ordered component names.
     *
     * @return string[]
     */
    public function getChildOrder(): array;

    /**
     * @param string[] $order
     */
    public function setChildOrder(array $order): void;
}
