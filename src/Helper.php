<?php
declare(strict_types=1);

namespace NubecuLabs\Components;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class Helper
{
    protected static $instance;

    private function __construct()
    {
    }

    /**
     * @static
     */
    public static function setInstance(Helper $instance): void
    {
        static::$instance = $instance;
    }

    /**
     * @static
     */
    public static function getInstance(): Helper
    {
        return static::$instance;
    }
}
