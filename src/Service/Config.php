<?php

namespace App\Service;

class Config
{
    /** @var array|null $instance */
    protected static $configuration = null;

    /**
     * @param string $key
     * @return mixed|null
     */
    public static function get(string $key)
    {
        if (self::$configuration === null) {
            $json = file_get_contents(__DIR__ . "/../../config.json");
            self::$configuration = json_decode($json, true);
        }

        if (isset(self::$configuration[$key])) {
            return self::$configuration[$key];
        }

        return null;
    }
}