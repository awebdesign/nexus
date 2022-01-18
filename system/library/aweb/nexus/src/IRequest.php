<?php

namespace Aweb\Nexus;

use Aweb\Nexus\Http\Request;
use Exception;

/**
 * extending Laravel Str helper
 */
class IRequest
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

    public static function getInstance($registry)
    {
        if (!self::$instance) {
            self::$instance = new Request(Nexus::request());
        }

        return self::$instance;
    }
}