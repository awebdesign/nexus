<?php

namespace Aweb\Nexus\Http;

use Aweb\Nexus\Nexus;
use Aweb\Nexus\Session;
use Aweb\Nexus\Validator;
use Aweb\Nexus\Support\Arr;
use stdClass;

class RequestInstance
{
    private $request;

    /**
     * Custom parameters.
     */
    private $attributes = [];

    /**
     * Old post data
     */
    private $old = [];

    /**
     * @param $nexus
     */
    public function __construct() {
        $this->request = Nexus::getRegistry('request');

        // flash old data
        $this->old = Session::getOldInput();

        if($this->getMethod() === 'POST') {
            Session::flashInput($this->post());
        }
    }

    // Singleton methods



    /**
     * Get current request method
     *
     * @return string
     */
    public function method(): string
    {
        return strtoupper($this->request->server['REQUEST_METHOD']);
    }

    /**
     * Gets a "parameter" value from any bag.
     *
     * This method is mainly useful for libraries that want to provide some flexibility. If you don't need the
     * flexibility in controllers, it is better to explicitly get request parameters from the appropriate
     * public property instead (attributes, query, request).
     *
     * Order of precedence: PATH (routing placeholders or custom attributes), GET, POST
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key = null, $default = null)
    {
        if(is_null($key)) {
            return array_merge($this->request->files, $this->request->post, $this->request->get, $this->attributes);
        }

        if (Arr::has($this->attributes, $key)) {
            return Arr::get($this->attributes, $key, $default);
        }

        if (Arr::has($this->request->get, $key)) {
            return Arr::get($this->request->get, $key, $default);
        }

        if (Arr::has($this->request->post, $key)) {
            return Arr::get($this->request->post, $key, $default);
        }

        if (Arr::has($this->request->files, $key)) {
            return Arr::get($this->request->files, $key, $default);
        }

        return $default;
    }

    /**
     * Set value on request ... call registry->set()
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value): void
    {
        Arr::set($this->attributes, $key, $value);
    }

    /**
     * Retrieve a request payload item from the request.
     * Get request->post[key]. If nothing found, default is returned instead
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function post(string $key = null, $default = null)
    {
        if(is_null($key)) {
            return $this->request->post;
        }

        return Arr::get($this->request->post, $key, $default);
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
        return isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'));
    }

    /**
     * Return Request port
     *
     * @return integer
     */
    public function getPort(): int
    {
        return Arr::get($this->request->server, 'SERVER_PORT');
    }

    /**
     * Returns request->get array
     *
     * @return array
     */
    public function getQuery(): array
    {
        return $this->request->get;
    }

    /**
     * Returns query params as string
     *
     * @return string
     */
    public function getQueryString(): string
    {
        return html_entity_decode(Arr::get($this->request->server, 'QUERY_STRING'));
    }

    /**
     * Returns server host
     *
     * @return string
     */
    public function getHost(): string
    {
        return $this->request->server['SERVER_NAME'];
    }

    /**
     * Call request->server['REQUEST_METHOD'] = = $method
     *
     * @param string $method
     * @return void
     */
    public function setMethod(string $method): void
    {
        $this->request->server['REQUEST_METHOD'] = strtoupper($method);
    }

    /**
     * returns request->server['REQUEST_METHOD'] =
     *
     * @return string
     */
    public function getMethod(): string
    {
        return strtoupper($this->request->server['REQUEST_METHOD']);
    }

    /**
     * request->server[key] ?? default
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function server(string $key = null, $default = null)
    {
        return Arr::get($this->request->server, $key, $default);
    }

    /**
     * Determine if the request contains a given input item key.
     *
     * @param string $key
     * @return boolean
     */
    public function has(string $key): bool
    {
        return Arr::has($this->attributes, $key) || Arr::has($this->request->get, $key) || Arr::has($this->request->post, $key);
    }

    /**
     * call has foreach in array, returns at first true found
     *
     * @param array $keys
     * @return boolean
     */
    public function hasAny(array $keys): bool
    {
        foreach ($keys as $key) {
            if ($this->has($key)) {
                return true;
            }
        }

        return false;
    }

    // private
    private function isEmptyString($value)
    {
        return !is_bool($value) && ! is_array($value) && trim((string) $value) === '';
    }


    /**
     * Determine if the request contains a non-empty value for an input item. accept arrays too. Fails if at least one key is empty
     *
     * @param  string|array  $key
     * @return bool
     */
    public function filled($key): bool
    {
        $keys = (array)$key;
        foreach ($keys as $key) {
            if ($this->isEmptyString($this->get($key))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the request contains a non-empty value for an input item. accept arrays too. Pass if at least one key is filled
     *
     * @param  string|array  $key
     * @return bool
     */
    public function anyFilled($keys): bool
    {
        $keys = (array)$keys;
        foreach ($keys as $key) {
            if (!$this->isEmptyString($this->get($key))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the keys for all of the input and files.
     * array_keys(array_merge(registry->all, request->post, request->get))
     *
     * @return array
     */
    public function keys(): array
    {
        $keys = array_merge(
            array_keys($this->attributes),
            array_keys($this->request->get),
            array_keys($this->request->post)
        );

        return array_unique($keys);
    }

    /**
     * array_merge(request->post, request->get, registry->all);
     *
     * @param array|null $keys [key => value] default for missing or empty values
     * @return array
     */
    public function all(): array
    {
        return array_merge($this->request->post, $this->request->get, $this->attributes);
    }

    /**
     * return request->post[x] ?? default
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function input(string $key = null, $default = null)
    {
        return $this->post($key, $default);
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
            $value = Arr::get($input, $key, $placeholder);

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
    public function query($key = null, $default = null)
    {
        return Arr::get($this->request->get, $key, $default);
    }

    /**
     * retrievs the given file, or all files from OC $this->reques->files
     *
     * @return void
     */
    public function files($key = null)
    {
        if (is_null($key)) {
            return $this->request->files;
        }

        return Arr::get($this->request->files, $key);
    }

    /**
     * A way to set many custom attributes on request
     *
     * @param array $data
     * @return void
     */
    public function merge(array $data): void
    {
        $this->attributes = array_merge($this->attributes, $data);
    }

    /**
     * Get old input, aka request->post
     *
     * @param string $name
     * @param mixed $default
     * @return void
     */
    public function old(string $name = '', $default = null)
    {
        return Arr::get($this->old, $name, $default);
    }

    /**
     * Validate the given request with the given rules.
     *
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return array
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate(array $rules, array $messages = [], array $customAttributes = [])
    {
        $validator = Validator::make($this->get(), $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            $errors = $validator->errors();
            Session::flash('_errors', $errors->firstOfAll());

            $this->back();
        }
    }

    /**
     * Redirect back to url where request come from
     *
     * @return void
     */
    public function back($code = 302)
    {
        list($url) = Session::get('_last_url');

        Nexus::getRegistry('response')->redirect($url, $code);
    }

// TODO:
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