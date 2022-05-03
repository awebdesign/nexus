<?php

namespace Aweb\Nexus;

use RuntimeException;
use Aweb\Nexus\Db;
use Aweb\Nexus\Database\Schema\Builder;


class Schema
{
    /**
     * Query Builder
     */
    protected static $instance;

    private function __construct() {}

    /**
     * Get DB instance
     */
    public static function getInstance(): Builder
    {
        if (!self::$instance) {

            if(!class_exists('Aweb\Nexus\Database\PdoAdapter')) {
                throw new RuntimeException('Nexus PdoAdapter was not loaded! Check ocmod file.');
            }
            Db::setTablePrefix(DB_PREFIX);
            self::$instance = Db::getSchemaBuilder();
        }

        return self::$instance;
    }

    public static function __callStatic($name, $arguments = [])
    {
        if (!method_exists(self::getInstance(), $name)) {
            throw new RuntimeException("Method $name does not exists on ". get_class(self::getInstance()));
        }

        return call_user_func_array([self::getInstance(), $name], $arguments);
    }
}