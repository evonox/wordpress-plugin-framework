<?php
/*
 * Plugin Name:       Wordpress Plugin Framework Demo
 * Plugin URI:        https://github.com/evonox/wp-plugin-framework.git
 * Description:       Wordpress Plugin Framework Demo
 * Version:           1.0.0
 * Author:            Viktor Prehnal
 * Author URI:        https://www.viktorprehnal.cz/
 * License:           MIT
 * License URI:       https://opensource.org/license/mit
 */

if(! defined("ABSPATH")) {
    exit;
}

define('__PLUGIN___MAIN_FILE_PATH', __FILE__);
require_once __DIR__ . "/framework/bootstrap.php";
