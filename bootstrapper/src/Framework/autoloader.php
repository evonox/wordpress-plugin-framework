<?php

if (! defined('ABSPATH')) {
    exit;
}

spl_autoload_register(
    function ($class_name) {

        // Load only classes starting with the "__PLUGIN__" namespace.
        if (strpos($class_name, '__PLUGIN__\\') !== 0) {
            return;
        }

        // Remove the namespace prefix.
        $relative_class = substr($class_name, strlen('__PLUGIN__\\'));

        // Convert namespace separators to directory separators.
        $relative_path = strtolower(str_replace('\\', '/', $relative_class));

        // Convert class name to WordPress-style file name.
        $file_name = 'class-' . str_replace('_', '-', $relative_path) . '.php';

        // File is located directly in the plugin root.
        $base_dir = plugin_dir_path(__PLUGIN___MAIN_FILE_PATH);
        $file_path = $base_dir . $file_name;

        // Security: load only existing files within the plugin directory.
        if (file_exists($file_path)) {
            require_once $file_path;
        }
    }
);
