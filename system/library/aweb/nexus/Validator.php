<?php

namespace Aweb\Nexus;

use Rakit\Validation\Validator as ValidationValidator;
use Rakit\Validation\Validation;

class Validator
{
    protected static $instance;

    public static function getInstance()
    {
        if (!self::$instance) {
            $instance = new ValidationValidator();
            self::loadLang($instance);

            self::$instance = $instance;
        }

        return self::$instance;
    }

    public static function make(array $inputs, array $rules, array $messages = []): Validation
    {
        $i = self::getInstance()->make($inputs, $rules, $messages);
        $i->validate();

        return $i;
    }

    protected static function loadLang($validator)
    {
        if (defined('DIR_CATALOG')) {
            $language_code = config('config_admin_language');
        } else {
            $language_code = session('language');
        }

        $lang = null;
        switch($language_code) {
            case 'ro-ro':
            case 'romana':
            case 'romanian':
                $lang  = 'romanian';
            break;
        }

        if($lang) {
            $lang_file = realpath("./lang/{$lang}.php");
            if (file_exists($lang_file)) {
                $lang_keys = require_once($lang_file);
                //TODO:
                return;
            }
        }
    }
}