<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Expla_Test/includes
 * @author     Roman Peniaz <roman.peniaz@gmail.com>
 */
class Expla_Test_Activator {

	public static function activate() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-expla-cron-updater.php';
		Expla_Cron_Updater::schedule_cron_events();

	}

}
