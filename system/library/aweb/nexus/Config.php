<?php

namespace Aweb\Nexus;

use Aweb\Nexus\Support\Arr;
use Exception;

class Config
{
    private function __construct() {}

    /**
     * Get key from config, dot notation allowed
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $config = Nexus::getRegistry('config');
        if (strpos($key, '.')) {
            $path = explode('.', $key);
            $key = array_shift($path);
            $path = implode('.', $path);
            $val = $config->get($key);

            return Arr::get($val, $path, $default);
        }

        $val = $config->get($key);
        if (self::isEmptyString($val)) {
            return $default;
        }

        return $val;
    }

    /**
     * call $registry->config->set($value); If key is an associative array, call set foreach (key => value) of this array
     *
     * @param array|string $key
     * @param mixed|null $value
     * @return void
     */
    public static function set($key, $value = null): void
    {
        $config = Nexus::getRegistry('config');

        if (is_array($key)) {
            foreach ($key as $k => $value) {
                $config->set($k, $value);
            }
            return;
        }

        if (strpos($key, '.')) {
            $path = explode('.', $key);
            $key = array_shift($path);
            $path = implode('.', $path);
            $data = self::get($key, []);
            Arr::set($data, $path, $value);
            $config->set($key, $data);
        } else {
            $config->set($key, $value);
        }
    }

    /**
     * call opencart model_setting_setting->getSetting($code). Returns array of settings for module_code, or empty array if none found
     *
     * @param string $code
     * @return array
     */
    public static function all(string $code)
    {
        // check if model loaded
        try {
            Nexus::getRegistry('model_setting_setting');
        } catch(Exception $e) {
            Nexus::getRegistry('load')->model('setting/setting');
        }

        return Nexus::getRegistry('model_setting_setting')->getSetting($code);
    }

    // private
    private static function isEmptyString($value)
    {
        return !is_bool($value) && ! is_array($value) && trim((string) $value) === '';
    }
}