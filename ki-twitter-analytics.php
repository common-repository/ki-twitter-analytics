<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://waelhassan.com
 * @since             1.0.0
 * @package           ki-twitter-analytics
 *
 * @wordpress-plugin
 * Plugin Name:       Ki Twitter Analytics
 * Plugin URI:        https://ki.social
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.4
 * Author:            Wael Hassan
 * Author URI:        https://waelhassan.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ki-twitter-analytics
 * Domain Path:       /languages
 */
//namespace KiTwitterAnalytics;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('KI_TWITTER_ANALYTICS_VERSION', '1.0.4');
define( 'KI_TWITTER_ANALYTICS_BASE_URL', plugin_dir_url( __FILE__ ) );
define( 'KI_TWITTER_ANALYTICS_BASE_DIR', plugin_dir_path( __FILE__ ) );
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ki_inbox-activator.php
 */
function activate_ki_inbox()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-ki_inbox-activator.php';
    Ki_inbox_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ki_inbox-deactivator.php
 */
function deactivate_ki_inbox()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-ki_inbox-deactivator.php';
    Ki_inbox_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_ki_inbox');
register_deactivation_hook(__FILE__, 'deactivate_ki_inbox');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-ki_inbox.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ki_twitter_analytics()
{

    $plugin = new Ki_inbox();
    $plugin->run();

}

run_ki_twitter_analytics();
