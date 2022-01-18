<?php

namespace Aweb\Nexus\Http;

use Aweb\Nexus;
use Aweb\Nexus\Support\Arr;
use stdClass;

class Request
{
    private $request;

    /**
     * Opencart request object
     *
     * @param $request
     */
    public function __construct($request) {
        $this->request = $request;
    }

    // Singleton methods
    public function method(): string
    {
        return $this->request->server['REQUEST_METHOD'];
    }

    /**
     * Get request key in the following order of fallback: registry, query string, post, null. If nothing found, fallback is returned instead
     *
     * @param string $key
     * @param mixed $fallback
     * @return mixed
     */
    public function get(string $key, mixed $fallback = null): mixed
    {

    }

    /**
     * Set value on request ... call registry->set()
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, mixed $value): void
    {

    }

    /**
     * Get request->post[key]. If nothing found, fallback is returned instead
     *
     * @param string $key
     * @param mixed $fallback
     * @return void
     */
    public function post(string $key, mixed $fallback = null)
    {

    }

    /**
     * Gets the request's scheme.
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->isSecure() ? 'https' : 'http';
    }

    /**
     * Checks whether the request is secure or not.
     *
     * @return bool
     */
    public function isSecure(): bool
    {

    }

    /**
     * Return Request port
     *
     * @return integer
     */
    public function getPort(): int
    {

    }

    /**
     * Returns request->get array
     *
     * @return array
     */
    public function getQuery(): array
    {

    }

    /**
     * Returns query params as string
     *
     * @return string
     */
    public function getQueryString(): string
    {

    }

    /**
     * Returns server host
     *
     * @return string
     */
    public function getHost(): string
    {

    }

    /**
     * Call request->server['method'] = $method
     *
     * @param string $method
     * @return void
     */
    public function setMethod(string $method): void
    {

    }

    /**
     * returns request->server['method']
     *
     * @return string
     */
    public function getMethod(): string
    {

    }

    /**
     * request->server[key] ?? fallback
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function server(string $key = null, mixed $default = null): mixed
    {

    }

    /**
     * isset(request->get[key]) || isset(request->post[key])
     *
     * @param string $key
     * @return boolean
     */
    public function has(string $key): bool
    {

    }

    /**
     * call has foreach in array, returns at first true found
     *
     * @param array $keys
     * @return boolean
     */
    public function hasAny(array $keys): bool
    {

    }

    /**
     * Determine if the request contains a non-empty value for an input item. accept arrays too. Fails if at least one key is empty
     *
     * @param  string|array  $key
     * @return bool
     */
    public function filled($key): bool
    {

    }

    /**
     * Determine if the request contains a non-empty value for an input item. accept arrays too. Pass if at least one key is filled
     *
     * @param  string|array  $key
     * @return bool
     */
    public function anyFilled($keys): bool
    {

    }

    /**
     * array_keys(array_merge(registry->all, request->post, request->get))
     *
     * @return array
     */
    public function keys(): array
    {

    }

    /**
     * array_merge(registry->all, request->post, request->get);
     *
     * @param array|null $keys [key => value] fallback for missing or empty values
     * @return array
     */
    public function all($keys = null): array
    {

    }

    /**
     * return request->post[x] ?? default
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function input(string $key = null, mixed $default = null): mixed
    {

    }

    /**
     * Get a subset containing the provided keys with values from the input data.
     *
     * @param  array|mixed  $keys
     * @return array
     */
    public function only($keys)
    {
        $results = [];

        $input = $this->all();

        $placeholder = new stdClass;

        foreach (is_array($keys) ? $keys : func_get_args() as $key) {
            $value = data_get($input, $key, $placeholder);

            if ($value !== $placeholder) {
                Arr::set($results, $key, $value);
            }
        }

        return $results;
    }


    /**
     * Get all of the input except for a specified array of items.
     *
     * @param  array|mixed  $keys
     * @return array
     */
    public function except($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        $results = $this->all();

        Arr::forget($results, $keys);

        return $results;
    }

    /**
     * Retrieve a query string item from the request. request->get[key] ?? default
     *
     * @param  string|null  $key
     * @param  string|array|null  $default
     * @return string|array|null
     */
    public function query($key = null, $default = null): mixed
    {

    }

    public function merge()
    {
        //TODO:
    }

// public function hasCookie($key)
// public function cookie($key = null, $default = null)
// public function allFiles()
// public function hasFile($key)
// public function file($key = null, $default = null)
// public function root()
// public function url()
// public function fullUrl()
// public function path()
// public function routeIs(...$patterns)
// public function ajax()
// public function secure()
// public function ip()
// public function ips()
// public function userAgent()
// public function getUser():
// public function replace(array $input)
// public function getMimeType($format)
// public static function getMimeTypes($format)
// public function getContentType()
// public function isMethod($method)
// public function getCharsets()
// public function getEncodings()
// public function isXmlHttpRequest()


// public function json($key = null, $default = null)


}