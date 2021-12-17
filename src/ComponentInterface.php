<?php
declare(strict_types=1);

namespace ThenLabs\Components;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use ThenLabs\Components\Event\Event;

/**
 * Base component contract.
 *
 * This type of components do not have children and can only be contained
 * by components of type CompositeComponentInterface.
 *
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
interface ComponentInterface extends DependentInterface
{
    /**
     * Returns the component identifier.
     *
     * The component indentifier is used for reference the component internally.
     * Should be an unique value.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Returns the component name.
     *
     * The component name is a value sets by the user.
     *
     * @return string
     */
    public function getName(): ?string;

    /**
     * Sets the component name.
     *
     * @param string $name
     */
    public function setName(?string $name);

    /**
     * Returns the direct parent of the component.
     *
     * @return CompositeComponentInterface|null
     */
    public function getParent(): ?CompositeComponentInterface;

    /**
     * Returns an array with all the parent of the component.
     *
     * The first item is the direct parent while the last is the root of the tree.
     *
     * @return CompositeComponentInterface[]
     */
    public function getParents(): array;

    /**
     * Sets the component parent.
     *
     * @param CompositeComponentInterface $parent           The parent
     * @param boolean                     $addChildToParent Indicates whether the component should be added as a child of the parent.
     * @param boolean                     $dispatchEvents   Indicates if the operation will trigger events.
     */
    public function setParent(?CompositeComponentInterface $parent, bool $addChildToParent = true, bool $dispatchEvents = true);

    /**
     * Use this method for iterate over each parent of the component.
     *
     * @see ComponentInterface::getParents()
     *
     * @return Generator
     */
    public function parents(): iterable;

    /**
     * Returns only the dependencies of the component.
     *
     * @return DependencyInterface[]
     */
    public function getOwnDependencies(): array;

    /**
     * Returns other dependencies that the component may also have.
     *
     * The most common type of additional dependencies are those with other
     * components that are referenced from the current one.
     *
     * @return DependencyInterface[]
     */
    public function getAdditionalDependencies(): array;

    /**
     * Returns all the dependencies of the component.
     *
     * Include own and additional dependencies. If the component is an instance of
     * CompositeComponentInterface then it also includes the dependencies of its children.
     *
     * @return DependencyInterface[]
     */
    public function getDependencies(): array;

    /**
     * Returns the event dispatcher of the component.
     *
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher(): EventDispatcherInterface;

    /**
     * Sets the event dispatcher.
     *
     * @param EventDispatcherInterface
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher);

    /**
     * Adds an event listener on the event dispatcher of the component.
     *
     * @see EventDispatcherInterface::addListener()
     *
     * @param  string   $eventName
     * @param  callable $listener
     */
    public function on(string $eventName, callable $listener);

    /**
     * Removes an event listener from the event dispatcher of the component.
     *
     * @see EventDispatcherInterface::removeListener()
     *
     * @param  string   $eventName
     * @param  callable $listener
     */
    public function off(string $eventName, callable $listener);

    /**
     * Dispatches an event to all registered listeners.
     *
     * Optionally may triggers capturing and propagation of the event on the tree.
     *
     * @see EventDispatcherInterface::dispatch()
     *
     * @param  string   $eventName
     * @param  Event    $event     The object of the event.
     * @param  boolean  $capture   Indicates whether to capture the event.
     * @param  boolean  $bubbles   Indicates whether the event should be propagated.
     */
    public function dispatchEvent(string $eventName, Event $event, bool $capture = true, bool $bubbles = true);

    /**
     * Returns all data assigned to the component.
     *
     * @return array
     */
    public function getAllData(): array;

    /**
     * Sets a custom data to the component.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function setData(string $key, $value);

    /**
     * Returns a data of the component.
     *
     * If data not exists returns null.
     *
     * @param  string $key
     * @return mixed
     */
    public function getData(string $key);

    /**
     * Checks if component has one data.
     *
     * @param  string  $key
     * @return boolean
     */
    public function hasData(string $key): bool;

    /**
     * Find a data for all the parents of the component.
     *
     * The search is made with a route from the direct father to the root of the tree.
     *
     * @param  string $key
     * @param  bool   $currentFirst
     * @return mixed  The first data found.
     */
    public function getTopData(string $key, bool $currentFirst = true);
}
