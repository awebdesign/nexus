<?php

namespace Aweb\Nexus;

use Aweb\Nexus\Http\RequestInstance;
use Exception;

/**
 * @method static string method() Get current request method
 * @method static mixed get(string $key = null, $default = null) Gets a "parameter" value from any bag.
    * This method is mainly useful for libraries that want to provide some flexibility. If you don't need the
    * flexibility in controllers, it is better to explicitly get request parameters from the appropriate
    * public property instead (attributes, query, request).
    * Order of precedence: PATH (routing placeholders or custom attributes), GET, POST
 * @method static void set(string $key, $value) Set value on request ... call registry->set()
 * @method static mixed post(string $key = null, $default = null) Retrieve a request payload item from the request. Get request->post[key]. If nothing found, default is returned instead
 * @method static mixed getScheme() Gets the request's scheme.
 * @method static bool isSecure() Checks whether the request is secure or not.
 * @method static int getPort() Return Request port
 * @method static array getQuery() Returns request->get array
 * @method static string getQueryString() Returns query params as string
 * @method static string getHost() Returns server host
 * @method static void setMethod(string $method) Call request->server['REQUEST_METHOD'] = $method
 * @method static string getMethod() returns request->server['REQUEST_METHOD']
 * @method static mixed server(string $key = null, $default = null) Get OpenCart $request->server[$key] ?? $default
 * @method static bool has(string $key) Determine if the request contains a given input item key.
 * @method static bool hasAny(array $keys) call has foreach in array, returns at first true found
 * @method static bool filled($key) Determine if the request contains a non-empty value for an input item. accept arrays too. Fails if at least one key is empty
 * @method static bool anyFilled($keys) Determine if the request contains a non-empty value for an input item. accept arrays too. Pass if at least one key is filled
 * @method static array keys() Get the keys for all of the input and files. array_keys(array_merge(registry->all, request->post, request->get))
 * @method static array all() Returns array_merge(request->post, request->get, registry->all);
 * @method static mixed input(string $key = null, $default = null) Get OpenCart $request->post[$key] ?? default
 * @method static array only($keys) Get a subset containing the provided keys with values from the input data.
 * @method static array except($keys) Get all of the input except for a specified array of items.
 * @method static mixed query($key = null, $default = null) Retrieve a query string item from the request. request->get[key] ?? default
 * @method static void merge(array $data) A way to set many custom attributes on request
 * @method static mixed old(string $name = '', $default = null) Get old input, aka request->post
 * @method static void validate(array $rules, array $messages = [], array $customAttributes = []) Validate the given request with the given rules.
 * @method static void back($code = 302) Redirect back to url where request come from
 */
class Request
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
    public static function getInstance(): RequestInstance
    {
        if (!self::$instance) {
            self::$instance = new RequestInstance();
        }

        return self::$instance;
    }

    public static function __callStatic($name, $arguments = [])
    {
        if (!method_exists(self::getInstance(), $name)) {
            throw new Exception("Method $name does not exists on ". get_class(self::getInstance()));
        }

        return call_user_func_array([self::getInstance(), $name], $arguments);
    }
}