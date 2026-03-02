<?php

// @phpstan-ignore requireOnce.fileNotFound
require_once "wp-load.php";
require_once __DIR__ . "/../vendor/autoload.php";

spl_autoload_register(function ($class) {
    if (strpos($class, "__PLUGIN__") === 0) {
        $class = str_replace("__PLUGIN__", "", $class);
        $filePath = __DIR__ . "/.." . $class . ".php";
        $filePath = str_replace("\\", DIRECTORY_SEPARATOR, $filePath);
        require_once $filePath;
    }

    if (method_exists($class, 'initHooks')) {
        call_user_func([$class, 'initHooks']);
    }
});
