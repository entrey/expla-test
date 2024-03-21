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
 * @package           ExplaTest
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

function activateExplaTest()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-activator.php';
    ExplaTest\Activator::activate();
}

function deactivateExplaTest()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-deactivator.php';
    ExplaTest\Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activateExplaTest');
register_deactivation_hook(__FILE__, 'deactivateExplaTest');

/** The core plugin class. */
require plugin_dir_path(__FILE__) . 'includes/class-core.php';

function runExplaTest()
{
    $plugin = new ExplaTest\Core();
    $plugin->run();
}
runExplaTest();
