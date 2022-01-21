<?php

namespace Aweb\Nexus\Http;

use Aweb\Nexus\Nexus;
use Aweb\Nexus\Support\Arr;

class SessionInstance
{
    private $session;

    private $flashBag = '_flash';

    // each time this will be constructed => on each request, flashed data will be moved on attributes, then deleted
    private $flashed = [];

    /**
     * @param $nexus
     */
    public function __construct() {
        $this->session = Nexus::getRegistry('session');

        if (isset($this->session->data[$this->flashBag])) {
            $this->flashed = $this->session->data[$this->flashBag];
        }

        unset($this->session->data[$this->flashBag]);
    }

    /**
     * Checks if an attribute is defined. Dot notation allowed
     *
     * @return bool
     */
    public function has(string $name)
    {
        return data_has($this->session->data, $name) || data_has($this->flashed, $name);
    }

    /**
     * Returns an attribute. Dot notation allowed. Check in persist, then in flash
     *
     * @param mixed $default The default value if not found
     *
     * @return mixed
     */
    public function get(string $name, $default = null)
    {
        return data_get($this->session->data, $name, function() use ($name, $default) {
            if (isset($this->flashed)) {
                return data_get($this->flashed, $name, $default);
            }
            return $default;
        });
    }

    /**
     * Sets an attribute. Dot notation allowed
     *
     * @param mixed $value
     */
    public function set(string $name, $value)
    {
        data_set($this->session->data, $name, $value);
    }

    /**
     * Sets an attribute. Dot notation allowed
     *
     * @param mixed $value
     */
    public function put(string $name, $value)
    {
        data_set($this->session->data, $name, $value);
    }

    /**
     * Retrieve an attribute then remove it from session. Dot notation allowed
     *
     * @param mixed $value
     */
    public function pull(string $name, $default = null)
    {
        $val = data_get($this->session->data, $name, function() use ($name, $default) {
            $val = data_get($this->flashed, $name);
            if (!is_null($val)) {
                Arr::forget($this->flashed, $name);
                return $val;
            }

            return $default;
        });
        Arr::forget($this->session->data, $name);

        return $val;
    }

    /**
     * If your session data contains an integer you wish to increment or decrement
     * returns incremented value
     */
    public function increment(string $name, $incrementBy = 1): int
    {
        $val = (int) data_get($this->session->data, $name, 0);
        $val += $incrementBy;
        data_set($this->session->data, $name, $val);

        return $val;
    }

    /**
     * If your session data contains an integer you wish to increment or decrement
     * returns decremented value
     */
    public function decrement(string $name, $decrementBy = 1): int
    {
        $val = (int) data_get($this->session->data, $name, 0);
        $val -= $decrementBy;
        data_set($this->session->data, $name, $val);

        return $val;
    }

    /**
     * Returns attributes.
     *
     * @return array
     */
    public function all()
    {
        $returned = array_merge($this->session->data, $this->flashed);
        unset($returned[$this->flashBag]); // unset flashbag key

        return $returned;
    }

    /**
     * Removes an attribute.
     *
     * @return mixed The removed value or null when it does not exist
     */
    public function forget(string $name)
    {
        Arr::forget($this->session->data, $name);
        Arr::forget($this->flashed, $name);
    }

    /**
     * Clears all attributes. If $names are given, clear only them
     */
    public function flush(array $names = null)
    {
        if (!$names) {
            $this->session->data = [];
            $this->flashed = [];
        } else {
            foreach ($names as $name) {
                Arr::forget($this->session->data, $name);
                Arr::forget($this->flashed, $name);
            }
        }
    }

    /**
     * Get session id
     */
    public function getId()
    {
        return $this->session->data->getId();
    }

    /**
     * Get session name
     */
    public function getName()
    {
        return session_name();
    }

    /**
     * Returns Flash bag
     *
     * @return mixed
     */
    public function getFlashBag()
    {
        return $this->flashed;
    }

    /**
     * To persist your flash data only for the current request
     *
     * @return void
     */
    public function flash(string $name, $value = null)
    {
        data_set($this->flashed, $name, $value);
        data_set($this->session->data[$this->flashBag], $name, $value);
    }

    /**
     * If you need to persist your flash data for several requests
     *
     * @return void
     */
    public function reflash()
    {
        $this->session->data[$this->flashBag] = $this->flashed;
    }

    /**
     * If you only need to keep specific flash data (move them from _flash to permanent)
     * @param string|array $key to be moved from flashBag to persist.
     *
     * @return void
     */
    public function keep($key)
    {
        if (empty($this->flashed)) {
            return;
        }

        foreach ((array) $key as $k) {
            $val = data_get($this->flashed, $k);
            if (!is_null($val)) {
                data_set($this->session->data, $k, $val);
                Arr::forget($this->flashed, $k);
            }
        }
    }





    /**
     * Flash an input array to the session.
     *
     * @param  array  $value
     * @return void
     */
    public function flashInput(array $value)
    {
        $this->flash('_old_input', $value);
    }

    /**
     * Determine if the session contains old input.
     *
     * @param  string|null  $key
     * @return bool
     */
    public function hasOldInput($key = null)
    {
        $old = $this->getOldInput($key);

        return is_null($key) ? count($old) > 0 : ! is_null($old);
    }

    /**
     * Get the requested item from the flashed input array.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function getOldInput($key = null, $default = null)
    {
        return Arr::get($this->get('_old_input', []), $key, $default);
    }
}
