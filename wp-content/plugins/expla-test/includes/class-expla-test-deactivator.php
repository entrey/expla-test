<?php
/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Expla_Test/includes
 * @author     Roman Peniaz <roman.peniaz@gmail.com>
 */
class Expla_Test_Deactivator {

	public static function deactivate() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-expla-cron-updater.php';
		Expla_Cron_Updater::delete_scheduled_events();

	}

}
