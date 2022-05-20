<?php

namespace Aweb\Nexus;

use RuntimeException;
use Aweb\Nexus\Database\Connection;


class Db
{
    /**
     * Query Builder
     */
    protected static $instance;

    private function __construct() {}

    /**
     * Get DB instance
     */
    public static function getInstance(): Connection
    {
        if (!self::$instance) {

            if(!class_exists('Aweb\Nexus\Database\PdoAdapter')) {
                throw new RuntimeException('Nexus PdoAdapter was not loaded! Check ocmod file.');
            }

            $db = Nexus::getRegistry('db');
            $pdo = $db->getActiveConnection();
            self::$instance = new Connection($pdo, DB_PREFIX);
        }

        return self::$instance;
    }

    public static function __callStatic($name, $arguments = [])
    {
        if (!method_exists(self::getInstance(), $name)) {
            throw new RuntimeException("Method $name does not exists on ". get_class(self::getInstance()));
        }

        // set OC table prefix
        self::getInstance()->setDatabaseName(DB_DATABASE);
        self::getInstance()->setTablePrefix(DB_PREFIX);

        return call_user_func_array([self::getInstance(), $name], $arguments);
    }
}