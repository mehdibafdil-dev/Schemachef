<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/mehdibafdil-dev
 * @since             1.0.0
 * @package           Schemachef
 *
 * @wordpress-plugin
 * Plugin Name:       SchemaChef
 * Plugin URI:        https://github.com/mehdibafdil-dev
 * Description:       SchemaChef simplifies adding structured Recipe Schema Markup to your site, boosting your recipes' visibility in search results. Say goodbye to complex coding and make your culinary creations shine
 * Version:           1.0.3
 * Author:            Mehdi BAFDIL
 * Author URI:        https://github.com/mehdibafdil-dev
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       schemachef
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('SCHEMACHEF_VERSION', '1.0.3');
define('SCHEMACHEF_ADMIN_ASSETS_PATH', plugin_dir_url(__FILE__) . 'admin/');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-schemachef-activator.php
 */
function activate_schemachef()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-schemachef-activator.php';
	Schemachef_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-schemachef-deactivator.php
 */
function deactivate_schemachef()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-schemachef-deactivator.php';
	Schemachef_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_schemachef');
register_deactivation_hook(__FILE__, 'deactivate_schemachef');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-schemachef.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_schemachef()
{
	require_once 'vendor/autoload.php';
	$plugin = new Schemachef();
	$plugin->run();
}
run_schemachef();
