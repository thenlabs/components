<?php
declare(strict_types=1);

namespace ThenLabs\Components\Event;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
trait OrderEventTrait
{
    /**
     * @var string[]
     */
    protected $newOrder;

    /**
     * @var string[]
     */
    protected $oldOrder;

    /**
     * @return string[]
     */
    public function getNewOrder(): array
    {
        return $this->newOrder;
    }

    /**
     * @param string[] $newOrder
     */
    public function setNewOrder(array $newOrder): void
    {
        $this->newOrder = $newOrder;
    }

    /**
     * @return string[]
     */
    public function getOldOrder(): array
    {
        return $this->oldOrder;
    }

    /**
     * @param string[] $oldOrder
     */
    public function setOldOrder(array $oldOrder): void
    {
        $this->oldOrder = $oldOrder;
    }
}
