<?php

namespace ExplaTest;

/**
 * Fired during plugin deactivation.
 *
 *
 * @since   1.0.0
 * @package ExplaTest/includes
 * @author  Roman Peniaz <roman.peniaz@gmail.com>
 */
class Deactivator
{
    public static function deactivate()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-cron-updater.php';
        CronUpdater::deleteScheduledEvents();
    }
}
