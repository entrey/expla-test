<?php

namespace ExplaTest;

/**
 * Fired during plugin activation.
 *
 *
 * @since   1.0.0
 * @package ExplaTest/includes
 * @author  Roman Peniaz <roman.peniaz@gmail.com>
 */
class Activator
{
    public static function activate()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-cron-updater.php';
        CronUpdater::scheduleCronEvents();
    }
}
