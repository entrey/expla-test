<?php

namespace ExplaTest;

/**
 * The core plugin class.
 *
 * This is used to define admin-specific hooks, and
 * public-facing site hooks.
 *
 *
 * @package ExplaTest/includes
 * @author  Roman Peniaz <roman.peniaz@gmail.com>
 * @since   1.0.0
 */
class Core
{
    /**
     * @var Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * @var string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * @var string $version The current version of the plugin.
     */
    protected $version;

    public function __construct()
    {
        $this->version = '1.0.0';
        $this->plugin_name = 'expla-test';

        $this->loadDependencies();
        $this->defineAdminHooks();
        $this->definePublicHooks();
    }

    private function loadDependencies()
    {
        $dir_path = plugin_dir_path(dirname(__FILE__));

        /** Actions and filters. */
        require_once $dir_path . 'includes/class-loader.php';
        require_once $dir_path . 'admin/class-admin-functionality.php';
        require_once $dir_path . 'public/class-public-functionality.php';

        /** Cron updater manager. */
        require_once $dir_path . 'admin/class-cron-updater.php';

        /** Shortcode articles. */
        require_once $dir_path . 'admin/class-shortcode-articles.php';

        $this->loader = new Loader();
    }

    private function defineAdminHooks()
    {
        $plugin_admin = new AdminFunctionality($this->getPluginName(), $this->getVersion());
        new CronUpdater();

        $this->loader->addAction('admin_enqueue_scripts', $plugin_admin, 'enqueueStyles');
        $this->loader->addAction('admin_enqueue_scripts', $plugin_admin, 'enqueueScripts');
    }

    private function definePublicHooks()
    {
        $plugin_public = new PublicFunctionality($this->getPluginName(), $this->getVersion());
        $shortcode_articles = new ShortcodeArticles();

        $this->loader->addAction('wp_enqueue_scripts', $plugin_public, 'enqueueStyles');
        $this->loader->addAction('wp_enqueue_scripts', $plugin_public, 'enqueueScripts');
        $this->loader->addAction('wp_enqueue_scripts', $shortcode_articles, 'enqueueStyles');
    }

    public function run()
    {
        $this->loader->run();
    }

    public function getPluginName()
    {
        return $this->plugin_name;
    }

    public function getVersion()
    {
        return $this->version;
    }
}
