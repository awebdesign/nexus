<?php

namespace Aweb\Nexus;

use Exception;
use Aweb\Nexus\Database\Connection;


class Db
{
    /**
     * Query Builder
     */
    protected static $instance;

    private function __construct() {}

    /**
     * SET OpenCart registry as core, called only once
     */
    public static function getInstance(): Connection
    {
        if (!self::$instance) {
            $db = Nexus::getRegistry('db');
            $pdo = $db->getActiveConnection();
            self::$instance = new Connection($pdo, DB_PREFIX);
        }

        return self::$instance;
    }

    public static function __callStatic($name, $arguments = [])
    {
        if (!method_exists(self::getInstance(), $name)) {
            throw new Exception("Method $name does not exists on ". get_class(self::getInstance()));
        }

        // set OC table prefix
        self::getInstance()->setTablePrefix(DB_PREFIX);

        return call_user_func_array([self::getInstance(), $name], $arguments);
    }
}