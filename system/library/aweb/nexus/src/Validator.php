<?php

namespace Aweb\Nexus;

use Aweb\Nexus\Support\Str;
use Rakit\Validation\Validator as ValidationValidator;

class Validator
{
    protected static $instance;

    private function __construct() {}

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = self::configure();
        }

        return self::$instance;
    }

    protected static function configure()
    {
        $validator = new ValidationValidator();
        if (Str::endsWith(DIR_APPLICATION, 'admin/')) {
            $language_code = config('config_admin_language');
        } else {
            $language_code = session('language');
        }

        $lang_file = "./lang/$language_code.php";dd($lang_file);
        if (!file_exists($lang_file)) {
            return;
        }

        $lang_keys = require_once($lang_file);
dd($lang_keys);
        //todo: lang set

        return $validator;
    }

    // public static function
}