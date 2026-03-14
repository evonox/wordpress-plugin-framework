<?php

use __PLUGIN__\PluginMain;

if (! defined("ABSPATH")) {
    exit;
}

require_once __DIR__ . "/autoloader.php";

add_action("init", function () {
    $plugin = new PluginMain();
    $plugin->boot();
});

register_activation_hook(__PLUGIN___MAIN_FILE_PATH, function () {
    $plugin = new PluginMain();
    $plugin->boot();
    $plugin->onActivate();
});

register_deactivation_hook(__PLUGIN___MAIN_FILE_PATH, function () {
    $plugin = new PluginMain();
    $plugin->onDeactivate();
});

register_uninstall_hook(__PLUGIN___MAIN_FILE_PATH, function () {
    $plugin = new PluginMain();
    $plugin->onUninstall();
});
