<?php

use __PLUGIN__\PluginMain;

if (! defined("ABSPATH")) {
    exit;
}

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

function __PLUGIN__initialization(): void
{
    $plugin = new PluginMain();
    $plugin->boot();
}


function __PLUGIN___activation_hook(): void
{
    $plugin = new PluginMain();
    $plugin->boot();
    $plugin->onActivate();
}

function __PLUGIN___deactivation_hook(): void
{
    $plugin = new PluginMain();
    $plugin->onDeactivate();
}

function __PLUGIN___uninstall_hook(): void
{
    $plugin = new PluginMain();
    $plugin->onUninstall();
}

add_action("init", '__PLUGIN__initialization');

register_activation_hook(__PLUGIN___MAIN_FILE_PATH, '__PLUGIN___activation_hook');
register_deactivation_hook(__PLUGIN___MAIN_FILE_PATH, '__PLUGIN___deactivation_hook');
register_uninstall_hook(__PLUGIN___MAIN_FILE_PATH, '__PLUGIN___uninstall_hook');
