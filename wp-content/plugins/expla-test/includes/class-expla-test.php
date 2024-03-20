<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @package Expla_Test/includes
 * @author  Roman Peniaz <roman.peniaz@gmail.com>
 * @since   1.0.0
 */
class Expla_Test {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @var   Expla_Test_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @var   string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @var   string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 */
	public function __construct() {
		if ( defined( 'EXPLA_TEST_VERSION' ) ) {
			$this->version = EXPLA_TEST_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'expla-test';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	private function load_dependencies() {
		$dir_path = plugin_dir_path( dirname( __FILE__ ) );

		/** Actions and filters. */
		require_once $dir_path . 'includes/class-expla-test-loader.php';

		/** Internationalization functionality. */
		require_once $dir_path . 'includes/class-expla-test-i18n.php';

		/** Actions of admin area. */
		require_once $dir_path . 'admin/class-expla-test-admin.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-expla-db-manager.php';

		/** Actions of public-facing side. */
		require_once $dir_path . 'public/class-expla-test-public.php';

		$this->loader = new Expla_Test_Loader();
	}

	private function set_locale() {
		$plugin_i18n = new Expla_Test_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	private function define_admin_hooks() {
		$plugin_admin = new Expla_Test_Admin( $this->get_plugin_name(), $this->get_version() );
		$db_manager = new Expla_DB_Manager();

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
	}

	private function define_public_hooks() {
		$plugin_public = new Expla_Test_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}

}
