<?php

spl_autoload_register(function ($class) {
    $class = trim($class, '\\');
    $path = 'Aweb\Nexus\\';
    if (strpos($class, $path) === 0) {
        $class = str_replace($path, '', $class);
        $file = __DIR__.'/'.$class.'.php';
        $file = str_replace('\\', '/', $file);
        if (file_exists($file)) {
            require_once($file);
        }
    }
});