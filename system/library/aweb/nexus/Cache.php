<?php

namespace Aweb\Nexus;

class Cache
{
    private function __construct() {}

    /**
     * Get key from cache, dot notation not allowed
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $cache = Nexus::getRegistry('cache');
        $val = $cache->get($key);
        if (empty($val)) {
            return $default;
        }

        return $val;
    }

    /**
     * call $registry->cache->set($value);
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    //TODO: add ttl as a 3rd param
    public static function set($key, $value): void
    {
        $cache = Nexus::getRegistry('cache');

        $cache->set($key, $value);
    }

    /**
     * call $registry->cache->delete($value);
     *
     * @param string $key
     * @return void
     */
    public static function forget($key): void
    {
        $cache = Nexus::getRegistry('cache');

        $cache->delete($key);
    }
}