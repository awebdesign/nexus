<?php
/*
 * Created on Tue September 17 2021 by DaRock
 *
 * Aweb Design
 * https://www.awebdesign.ro
 */

namespace Aweb\Nexus;

use Exception;

/**
 * Can be customized from main config.php
 */
if(!defined('DIR_CATALOG_NAME')) {
    define('DIR_CATALOG_NAME', 'catalog');
}

/**
 * Can be customized from main config.php
 */
if(!defined('DIR_ADMIN_NAME')) {
    define('DIR_ADMIN_NAME', 'admin');
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|
*/

require_once(DIR_SYSTEM . 'library/aweb/nexus/vendor/autoload.php');

// use \Aweb\Nexus\Http\Request;
// use \Aweb\Nexus\Session\StartSession;
// use \Aweb\Nexus\Session\SessionManager;

class Nexus
{
    private static $instance;
    private static $registry;
    private $booted;

    // single instance only
    private function __construct() {}

    /**
     * App Instance
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function init($registry)
    {
        if($this->isBooted()) {
            return null; //already booted
        }

        self::$registry = $registry;

        $this->booted = true;
    }

    /**
     * return Opencart Registry
     *
     * @return mixed return Opencart Registry
     */
    public function getRegistry()
    {
        return self::$registry;
    }

    /**
     * Retrieves the OpenCart registry
     *
     * @return object
     */
    public static function registry($type)
    {
        $registry = self::$registry;

        $data = $registry->get($type);

        if(is_null($data)) {
            throw new Exception('sadas das dsadsa dsa');
        }

        return $data;
    }

    public static function request() {
        return self::registry('request');
    }
    //session
    //config
    //etc

    /**
     * Determine if the application has booted.
     *
     * @return bool
     */
    public function isBooted()
    {
        return $this->booted;
    }
}