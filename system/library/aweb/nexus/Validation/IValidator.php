<?php

namespace Aweb\Nexus\Validation;

use Rakit\Validation\Validator;

class IValidator
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
        $validator = new Validator;

        //todo: lang set

        return $validator;
    }
}