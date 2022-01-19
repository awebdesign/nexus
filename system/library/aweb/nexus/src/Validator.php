<?php

namespace Aweb\Nexus;

use Rakit\Validation\Validator as ValidationValidator;

class Validator
{
    protected $instance;

    private function __construct() {}

    public static function getInstance()
    {
        if (!self::$instance) {
            //todo: lang
            self::$instance = self::configure();
        }

        return self::$instance;
    }

    protected static function configure()
    {
        $validator = new ValidationValidator();

        //todo: lang set

        return $validator;
    }
}