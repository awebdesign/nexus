<?php
/*
 * Created on Tue September 17 2021 by DaRock
 *
 * Aweb Design
 * https://www.awebdesign.ro
 */

namespace Aweb\Nexus;

use RuntimeException;

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

class Nexus
{
    const VERSION = '2.1.01';

    private static $registry;
    private static $booted;

    /**
     * Empty constructor
     */
    private function __construct() {}

    public static function init($registry)
    {
        if(self::$booted) {
            return null; //already booted
        }

        self::$registry = $registry;

        self::$booted = true;

        self::urlPush();
    }

    /**
     * Retrieves the Nexus instance which has access to OC registry
     *
     * @return object
     */
    public static function getRegistry($type = null)
    {
        $registry = self::$registry;

        if(is_null($type)) {
            return $registry;
        }

        $data = $registry->get($type);

        if(is_null($data)) {
            throw new RuntimeException("There is not registry instance for {$type}");
        }

        return $data;
    }

    /**
     * keep a request url history in order to provide back() facility on request
     *
     * @return void
     */
    protected static function urlPush()
    {
        // compose current url
        $protocol = ((!empty(Request::server('HTTPS')) && Request::server('HTTPS') != 'off') || Request::server('SERVER_PORT') == 443) ? "https://" : "http://";
        $current = $protocol . Request::server('HTTP_HOST') . Request::server('REQUEST_URI');
        $current = html_entity_decode($current);

        $session = Nexus::getRegistry('session');
        if (empty($session->data['_last_url']) || !is_array($session->data['_last_url'])) {
            $data = [];
        } else {
            $data = $session->data['_last_url'];
        }

        array_unshift($data, $current);
        if (count($data) > 2) {
            array_pop($data);
        }

        $session->data['_last_url'] = $data;
    }

    public static function isBooted()
    {
        return self::$booted;
    }
}
