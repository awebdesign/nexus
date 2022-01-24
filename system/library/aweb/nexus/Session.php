<?php

namespace Aweb\Nexus;

use Aweb\Nexus\Http\SessionInstance;
use RuntimeException;

/**
 * @method static bool has(string $name) Checks if an attribute is defined. Dot notation allowed
 * @method static mixed get(string $name, $default = null) Returns an attribute. Dot notation allowed. Check in persist, then in flash
 * @method static void set(string $name, $value) Sets an attribute. Dot notation allowed
 * @method static void put(string $name, $value) Sets an attribute. Dot notation allowed
 * @method static mixed pull(string $name, $default = null) Retrieve an attribute then remove it from session. Dot notation allowed
 * @method static int increment(string $name, $incrementBy = 1) If your session data contains an integer you wish to increment or decrement, returns result. If no session key is defined, it initiate it as 0 then apply the operation.
 * @method static int decrement(string $name, $decrementBy = 1) If your session data contains an integer you wish to increment or decrement, returns result. If no session key is defined, it initiate it as 0 then apply the operation.
 * @method static array all() Returns attributes.
 * @method static void forget(string $name) Removes an attribute.
 * @method static void flush(array $names = null) Clears all attributes. If $names are given, clear only them
 * @method static string getId() Get session id
 * @method static string getName() Get session name
 * @method static array getFlashBag() Returns Flash bag
 * @method static void flash(string $name, $value = null) To persist your flash data only for the current request
 * @method static void reflash() If you need to persist your flash data for several requests
 * @method static void keep($key) If you only need to keep specific flash data (move them from _flash to permanent)
 * @method static void flashInput(array $value) Flash an input array to the session.
 * @method static bool hasOldInput($key = null) Determine if the session contains old input.
 * @method static array getOldInput($key = null, $default = null) Get the requested item from the flashed input array.
 */

class Session
{
    protected static $instance;

    /**
     * Empty constructor
     */
    private function __construct() {}

    /**
     * Get Session instance
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new SessionInstance();
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