<?php
ob_start();
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.fiverr.com/junaidzx90
 * @since             1.0.0
 * @package           Woocv
 *
 * @wordpress-plugin
 * Plugin Name:       Custom Variations
 * Plugin URI:        https://www.fiverr.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            junaidzx90
 * Author URI:        https://www.fiverr.com/junaidzx90
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocv
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WOOCV_VERSION', '1.0.0' );
date_default_timezone_set(get_option('timezone_string')?get_option('timezone_string'):'UTC');
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woocv-activator.php
 */
function activate_woocv() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocv-activator.php';
	Woocv_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woocv-deactivator.php
 */
function deactivate_woocv() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocv-deactivator.php';
	Woocv_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woocv' );
register_deactivation_hook( __FILE__, 'deactivate_woocv' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woocv.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-read-color.php';
$readColor = new ColorInterpreter;
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woocv() {

	$plugin = new Woocv();
	$plugin->run();

}
run_woocv();
