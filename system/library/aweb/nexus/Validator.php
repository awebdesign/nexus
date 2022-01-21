<?php

namespace Aweb\Nexus;

use Rakit\Validation\Validator as ValidationValidator;
use Rakit\Validation\Validation;

class Validator
{
    protected static $instance;
    protected static $lang = [];

    public static function getInstance(): ValidationValidator
    {
        if (!self::$instance) {
            $instance = new ValidationValidator();
            self::loadLang($instance);

            self::$instance = $instance;
        }

        return self::$instance;
    }

    public static function make(array $inputs, array $rules, array $messages = [], array $customAttributes = []): Validation
    {
        $validator = self::getInstance();
        $validation = $validator->make($inputs, $rules, $messages);

        // get oc translation keys.
        // foreach validated attribute, we check if $customAttributes provided, else check in oc translations.
        $languageKeys = Nexus::getRegistry('language')->all();
        foreach ($inputs as $name => $val) {
            if (isset($customAttributes[$name])) {
                $validation->setAlias($name, $customAttributes[$name]);
            }
            elseif (isset($languageKeys['entry_'.$name])) {
                $validation->setAlias($name, $languageKeys['entry_'.$name]);
            }
        }
        if ($customAttributes) {
            foreach ($customAttributes as $name => $val) {
                $validation->setAlias($name, $val);
            }
        }

        $validation->validate();

        return $validation;
    }

    protected static function loadLang($validator)
    {
        if (defined('DIR_CATALOG')) {
            $language_code = config('config_admin_language');
        } else {
            $language_code = session('language');
        }

        // load lang file and cache it per request
        if (!isset(self::$lang[$language_code])) {
            $lang = null;
            switch($language_code) {
                case 'ro-ro':
                case 'romana':
                case 'romanian':
                    $lang  = 'romanian';
                break;
            }

            if ($lang) {
                $lang_file = realpath(__DIR__."/lang/{$lang}.php");
                if (file_exists($lang_file)) {
                    self::$lang[$language_code] = require_once($lang_file);
                }
            }
        }

        if (!isset(self::$lang[$language_code])) {
            return $validator;
        }

        foreach (self::$lang[$language_code] as $rule => $message) {
            $validator->setMessage($rule, $message);
        }

        return $validator;
    }
}