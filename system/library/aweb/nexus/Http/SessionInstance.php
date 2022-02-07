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
        return Arr::has($this->session->data, $name) || Arr::has($this->flashed, $name);
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
        return Arr::get($this->session->data, $name, function() use ($name, $default) {
            if (isset($this->flashed)) {
                return Arr::get($this->flashed, $name, $default);
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
        Arr::set($this->session->data, $name, $value);
    }

    /**
     * Sets an attribute. Dot notation allowed
     *
     * @param mixed $value
     */
    public function put(string $name, $value)
    {
        Arr::set($this->session->data, $name, $value);
    }

    /**
     * Retrieve an attribute then remove it from session. Dot notation allowed
     *
     * @param mixed $value
     */
    public function pull(string $name, $default = null)
    {
        $val = Arr::get($this->session->data, $name, function() use ($name, $default) {
            $val = Arr::get($this->flashed, $name);
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
        $val = (int) Arr::get($this->session->data, $name, 0);
        $val += $incrementBy;
        Arr::set($this->session->data, $name, $val);

        return $val;
    }

    /**
     * If your session data contains an integer you wish to increment or decrement
     * returns decremented value
     */
    public function decrement(string $name, $decrementBy = 1): int
    {
        $val = (int) Arr::get($this->session->data, $name, 0);
        $val -= $decrementBy;
        Arr::set($this->session->data, $name, $val);

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
        Arr::set($this->flashed, $name, $value);
        Arr::set($this->session->data[$this->flashBag], $name, $value);
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
            $val = Arr::get($this->flashed, $k);
            if (!is_null($val)) {
                Arr::set($this->session->data, $k, $val);
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

    /**
     * Errors | Get/Set all errors
     *
     * @param mixed $key | if is an array or a string will be a setter
     * @param mixed $val | used for setter
     * @return mixed Arry or Void
     */
    public function errors($key = null, $val = null)
    {
        if(is_array($key)) {
            foreach($key as $k => $v) {
                $this->flash('_errors.'.$k, $v);
            }
        } elseif ($val) {
            $this->flash('_errors.'.$key, $val);
        }
        elseif($key) {
            return $this->get('_errors.'.$key, []);
        }

        return $this->get('_errors', []);
    }

    /**
     * Set warning message/messages
     *
     * @param mixed $key | if is an array or a string will be a setter
     * @param mixed $val | used for setter
     * @return mixed Arry or Void
     */
    public function warning($key = null, $val = null)
    {
        return $this->flashSpecial('_warning', $key, $val);
    }

    /**
     * Set success message/messages
     *
     * @param mixed $key | if is an array or a string will be a setter
     * @param mixed $val | used for setter
     * @return mixed Arry or Void
     */
    public function success($key = null, $val = null)
    {
        return $this->flashSpecial('_success', $key, $val);
    }

    /**
     * Used for special flash messages
     *
     * @param string $type
     * @param mixed $key
     * @param mixed $val
     * @return mixed
     */
    private function flashSpecial($type, $key = null, $val = null)
    {
        $messages = is_array($key) ? $key : [$key];

        if ($val) {
            $this->flash($type . '.' .$key, $val);
        } elseif(!empty($messages)) {
            foreach($messages as $k => $v) {
                $this->flash($type . '.' . $k, $v);
            }
        } else {
            return $this->get($type);
        }
    }
}
