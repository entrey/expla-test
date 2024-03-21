<?php

namespace ExplaTest;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package ExplaTest/admin
 * @author  Roman Peniaz <roman.peniaz@gmail.com>
 */
class AdminFunctionality
{
    /**
     * @var string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * @var string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version     The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueueStyles()
    {
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'css/expla-test-admin.css',
            [],
            $this->version,
            'all'
        );
    }

    public function enqueueScripts()
    {
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/expla-test-admin.js',
            [ 'jquery' ],
            $this->version,
            false
        );
    }
}
