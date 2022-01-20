<?php

namespace Aweb\Nexus;

use Aweb\Nexus\Http\SessionInstance;
use Exception;


class Session
{
    /**
     * Opencart registry
     */
    protected static $instance;

    /**
     * SET OpenCart registry as core
     */
    private function __construct() {}

    /**
     * SET OpenCart registry as core, called only once
     */

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new SessionInstance(Nexus::getInstance());
        }

        return self::$instance;
    }

    /**
     * If you need to persist your flash data for several requests
     * Note that this must be called before any session data access because data is cleared on __construct => getInstance
     *
     * @return void
     */
    public function reflash()
    {
        Nexus::registry('session')->data['_reflash'] = true;
    }

    public static function __callStatic($name, $arguments = [])
    {
        if (!method_exists(self::getInstance(), $name)) {
            throw new Exception("Method $name does not exists on ". get_class(self::getInstance()));
        }

        return call_user_func_array([self::getInstance(), $name], $arguments);
    }
}