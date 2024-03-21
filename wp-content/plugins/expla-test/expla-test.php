<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/entrey
 * @since             1.0.0
 * @package           Expla_Test
 *
 * @wordpress-plugin
 * Plugin Name:       Expla Test
 * Plugin URI:        https://github.com/entrey/expla-test
 * Description:       Plugin description.
 * Version:           1.0.0
 * Author:            Roman Peniaz
 * Author URI:        https://github.com/entrey/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       expla-test
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'EXPLA_TEST_VERSION', '1.0.0' );
define( 'EXPLA_TEST_MAIN_FILE_URL', plugin_dir_url( __FILE__ ) );
define( 'EXPLA_TEST_MAIN_FILE_PATH', plugin_dir_path( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-expla-test-activator.php
 */
function activate_expla_test() {
	require_once EXPLA_TEST_MAIN_FILE_PATH . 'includes/class-expla-test-activator.php';
	Expla_Test_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-expla-test-deactivator.php
 */
function deactivate_expla_test() {
	require_once EXPLA_TEST_MAIN_FILE_PATH . 'includes/class-expla-test-deactivator.php';
	Expla_Test_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_expla_test' );
register_deactivation_hook( __FILE__, 'deactivate_expla_test' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require EXPLA_TEST_MAIN_FILE_PATH . 'includes/class-expla-test.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_expla_test() {
	$plugin = new Expla_Test();
	$plugin->run();
}
run_expla_test();
