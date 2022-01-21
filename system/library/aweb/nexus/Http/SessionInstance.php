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
        $this->session = Nexus::getRegistry('session')->data;

        if (isset($this->session[$this->flashBag])) {
            $this->flashed = $this->session[$this->flashBag];
        }
print_r($this->flashed);
        unset($this->session[$this->flashBag]);
    }

    /**
     * Checks if an attribute is defined. Dot notation allowed
     *
     * @return bool
     */
    public function has(string $name)
    {
        return data_has($this->session, $name) || data_has($this->flashed, $name);
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
        return data_get($this->session, $name, function() use ($name, $default) {
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
        data_set($this->session, $name, $value);
    }

    /**
     * Sets an attribute. Dot notation allowed
     *
     * @param mixed $value
     */
    public function put(string $name, $value)
    {
        data_set($this->session, $name, $value);
    }

    /**
     * Sets an attribute. Dot notation NOT supported
     *
     * @param mixed $value
     */
    public function pull(string $name, $default = null)
    {
        $val = data_get($this->session, $name, function() use ($name, $default) {
            if (isset($this->flashed[$name])) {
                $value = $this->flashed[$name];
                unset($this->flashed[$name]);
                unset($this->session[$this->flashBag][$name]);

                return $value;
            }

            return $default;
        });
        unset($this->session[$name]);

        return $val;
    }

    /**
     * If your session data contains an integer you wish to increment or decrement
     * returns incremented value
     */
    public function increment(string $name, $incrementBy = 1): int
    {
        $val = (int) data_get($this->session, $name, 0);
        $val += $incrementBy;
        data_set($this->session, $name, $val);

        return $val;
    }

    /**
     * If your session data contains an integer you wish to increment or decrement
     * returns decremented value
     */
    public function decrement(string $name, $decrementBy = 1): int
    {
        $val = (int) data_get($this->session, $name, 0);
        $val -= $decrementBy;
        data_set($this->session, $name, $val);

        return $val;
    }

    /**
     * Returns attributes.
     *
     * @return array
     */
    public function all()
    {
        return $this->session;
    }

    /**
     * Removes an attribute.
     *
     * @return mixed The removed value or null when it does not exist
     */
    public function forget(string $name)
    {
        unset($this->session[$name]);
        unset($this->session[$this->flashBag][$name]);
    }

    /**
     * Clears all attributes. If $names are given, clear only them
     */
    public function flush(array $names = null)
    {
        if (!$names) {
            $this->session = [];
            $this->flashed = [];
        } else {
            foreach ($names as $name) {
                unset($this->session[$name]);
                unset($this->flashed[$name]);
                unset($this->session[$this->flashBag][$name]);
            }
        }
    }

    /**
     * Get session id
     */
    public function getId()
    {
        return $this->session->getId();
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
        $this->session[$this->flashBag][$name] = $this->flashed[$name] = $value;
    }

    /**
     * If you need to persist your flash data for several requests
     *
     * @return void
     */
    public function reflash()
    {
        $this->session[$this->flashBag] = $this->flashed;
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
            if (array_key_exists($k, $this->flashed)) {
                $this->session[$k] = $this->flashed[$k];
                unset($this->flashed[$k]);
                unset($this->session[$this->flashBag][$k]);
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
